// Код, отвечающий за операции в корзине
package com.example.cakeapp

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.provider.Settings
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.LinearLayout
import android.widget.RelativeLayout
import android.widget.TextView
import android.widget.Toast
import androidx.core.app.ActivityCompat
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.data.model.CartModel
import com.example.cakeapp.data.model.LoginResult
import com.example.cakeapp.data.model.Order
import com.example.cakeapp.placeholder.CartAdapter
import com.example.cakeapp.utils.ConnectionManager
import com.google.gson.Gson
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject


lateinit var activity_cart_Progressdialog: RelativeLayout
class CartActivity(val contextParam: Context,val fm:FragmentManager) : Fragment(), CartAdapter.Callbacks {

    lateinit var toolbar: androidx.appcompat.widget.Toolbar
    lateinit var textViewOrderingFrom: TextView
    lateinit var buttonPlaceOrder: Button
    lateinit var recyclerView: RecyclerView
    lateinit var layoutManager: RecyclerView.LayoutManager
    lateinit var menuAdapter: CartAdapter
    lateinit var restaurantId: String
    lateinit var restaurantName: String
    lateinit var linearLayout: LinearLayout
    lateinit var selectedItemsId: ArrayList<String>

    var totalAmount = 0.0

    var cartListItems = arrayListOf<CartModel>()
// Инициализация переменных и компонентов интерфейса: Здесь объявляются переменные для элементов пользовательского интерфейса, таких как кнопки, текстовые поля, адаптеры и т. д. Эти элементы затем настраиваются в методе onCreateView().
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {

        val view = inflater.inflate(R.layout.fragment_cart, container, false)

        buttonPlaceOrder = view.findViewById(R.id.btnSendOrder)
        textViewOrderingFrom = view.findViewById(R.id.textViewOrderingFrom)
        linearLayout = view.findViewById(R.id.linearLayout)
        toolbar = view.findViewById(R.id.toolBar)
        activity_cart_Progressdialog = view.findViewById(R.id.activity_cart_Progressdialog)


        buttonPlaceOrder.setOnClickListener(View.OnClickListener {
            if (cartListItems.count() < 1) return@OnClickListener
            else sendCart2Order()
        })

        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )

        val address = sp.getString("user_address", "").toString()
        textViewOrderingFrom.text = address
        layoutManager = LinearLayoutManager(contextParam)

        recyclerView = view.findViewById(R.id.recyclerViewCart)
        return view
    }
// Update: Обновляет данные в пользовательском интерфейсе фрагмента, основываясь на текущем состоянии корзины покупок. Этот метод вызывается для обновления общей суммы заказа при изменении содержимого корзины.
    override fun Update() {
        totalAmount = 0.0
        for (cm in cartListItems) {
            totalAmount += cm.Price * cm.Quantity
        }
        buttonPlaceOrder.text = "Заказать (" + totalAmount + " ₽.)"
    }
// sendCart2Order: Отправляет содержимое корзины покупок на сервер для создания нового заказа. Перед отправкой данных выполняется проверка наличия подключения к интернету и проверка аутентификации пользователя.
    fun sendCart2Order() {
        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )
        val id = sp.getString("user_id", "").toString()
        if (id == "") {

        } else {

            if (ConnectionManager().checkConnectivity(activity as Context)) {
                if (true) {

                    activity_cart_Progressdialog.visibility = View.VISIBLE
                    totalAmount = 0.0
                    for (cm in cartListItems) {
                        totalAmount += cm.Price * cm.Quantity
                    }
                    try {
                        val registerUser = JSONObject()
                        registerUser.put("UserID", id)
                        registerUser.put("Price", totalAmount)

                        val queue = Volley.newRequestQueue(activity as Context)
                        val url =
                            ("${resources.getString(R.string.url)}userorder.php?method=create")


                        val jsonObjectRequest = object : JsonObjectRequest(
                            Request.Method.POST,
                            url,
                            registerUser,

                            Response.Listener
                            {
                                val gson = Gson()
                                val result = gson.fromJson(it.toString(), LoginResult::class.java)

                                if (result.token != "" && result.message === null && result.status == 200) {
                                    val gson = Gson()
                                    val order = arrayListOf<Order>()
                                    val result =
                                        gson.fromJson(
                                            it.getString("records"),
                                            Array<Order>::class.java
                                        )
                                    order.addAll(result)
                                    sendCart2OrderDetail(order[0].ID)
                                    activity_cart_Progressdialog.visibility = View.INVISIBLE

                                } else if (result.token == null && result.message != null && result.status == 200) {

                                    Toast.makeText(
                                        contextParam,
                                        result.message,
                                        Toast.LENGTH_SHORT
                                    ).show()
                                }
                            },
                            Response.ErrorListener {
                                println("Error12 is " + it)
                                activity_cart_Progressdialog.visibility = View.INVISIBLE
                                println(it)
                                Toast.makeText(
                                    contextParam,
                                    "Some Error occurred!!!",
                                    Toast.LENGTH_SHORT
                                ).show()


                            }) {
                            override fun getHeaders(): MutableMap<String, String> {
                                val headers = HashMap<String, String>()

                                headers["Content-type"] = "application/json"
                                return headers
                            }
                        }

                        queue.add(jsonObjectRequest)

                    } catch (e: JSONException) {
                        Toast.makeText(
                            contextParam,
                            "Some unexpected error occured!!!",
                            Toast.LENGTH_SHORT
                        ).show()
                    }
                }
            } else {
                val alterDialog = androidx.appcompat.app.AlertDialog.Builder(activity as Context)

                alterDialog.setTitle("No Internet")
                alterDialog.setMessage("Internet Connection can't be establish!")
                alterDialog.setPositiveButton("Open Settings") { text, listener ->
                    val settingsIntent = Intent(Settings.ACTION_SETTINGS)
                    startActivity(settingsIntent)

                }

                alterDialog.setNegativeButton("Exit") { text, listener ->
                    ActivityCompat.finishAffinity(activity as Activity)
                }
                alterDialog.create()
                alterDialog.show()

            }
        }
    }
// sendCart2OrderDetail: Отправляет детали заказа (товары в корзине) на сервер для создания записей о заказанных товарах. Также выполняется проверка подключения к интернету и аутентификации пользователя.    
    private fun sendCart2OrderDetail(OrderID: String) {
        if (ConnectionManager().checkConnectivity(activity as Context)) {
            if (true) {

                activity_cart_Progressdialog.visibility = View.VISIBLE
                try {
                    val cartDetails = JSONArray()
                    for (cartListItem in cartListItems) {
                        val cartDetail = JSONObject()
                        cartDetail.put("OrderID", OrderID)
                        cartDetail.put("ProductID", cartListItem.ProductID)
                        cartDetail.put("Quantity", cartListItem.Quantity)
                        cartDetail.put("Price", cartListItem.Price)
                        cartDetails.put(cartDetail)
                    }
                    val sp = contextParam.getSharedPreferences(
                        R.string.shared_preferences.toString(),
                        Context.MODE_PRIVATE
                    )
                    val id = sp.getString("user_id", "").toString()
                    val registerUser = JSONObject()
                    registerUser.put("records", cartDetails)
                    registerUser.put("UserID", id)

                    val queue = Volley.newRequestQueue(activity as Context)
                    val url =
                        ("${resources.getString(R.string.url)}UserOrderDetail.php?method=createall")


                    val jsonObjectRequest = object : JsonObjectRequest(
                        Request.Method.POST,
                        url,
                        registerUser,

                        Response.Listener
                        {
                            val gson = Gson()
                            val result = gson.fromJson(it.toString(), LoginResult::class.java)

                            if (result.token != "" && result.status == 200) {
                                val gson = Gson()


                                Toast.makeText(
                                    contextParam,
                                    "Заказ сделан!",
                                    Toast.LENGTH_SHORT
                                ).show()
                                cartListItems.clear()
                                fetchData()
                                activity_cart_Progressdialog.visibility = View.INVISIBLE

                            } else if (result.token == null && result.message != null && result.status == 200) {

                                Toast.makeText(
                                    contextParam,
                                    result.message,
                                    Toast.LENGTH_SHORT
                                ).show()
                            }
                        },
                        Response.ErrorListener {
                            println("Error12 is " + it)
                            activity_cart_Progressdialog.visibility = View.INVISIBLE
                            println(it)
                            Toast.makeText(
                                contextParam,
                                "Some Error occurred!!!",
                                Toast.LENGTH_SHORT
                            ).show()


                        }) {
                        override fun getHeaders(): MutableMap<String, String> {
                            val headers = HashMap<String, String>()

                            headers["Content-type"] = "application/json"
                            return headers
                        }
                    }

                    queue.add(jsonObjectRequest)

                } catch (e: JSONException) {
                    Toast.makeText(
                        contextParam,
                        "Some unexpected error occured!!!",
                        Toast.LENGTH_SHORT
                    ).show()
                }
            }
        } else {
            val alterDialog = androidx.appcompat.app.AlertDialog.Builder(activity as Context)

            alterDialog.setTitle("No Internet")
            alterDialog.setMessage("Internet Connection can't be establish!")
            alterDialog.setPositiveButton("Open Settings") { text, listener ->
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)
                startActivity(settingsIntent)

            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(activity as Activity)
            }
            alterDialog.create()
            alterDialog.show()

        }
    }
// openMenuFragment: Метод для открытия фрагмента меню
    fun openMenuFragment() {
        val transaction = fragmentManager?.beginTransaction()

        transaction?.detach(this)
        transaction?.attach(this)
        transaction?.commit()
    }
// fetchData: Получает данные о корзине покупок с сервера и обновляет пользовательский интерфейс соответствующим образом. Перед запросом проверяется наличие интернет-соединения и аутентификация пользователя.
    fun fetchData() {

        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )
        val id = sp.getString("user_id", "").toString()
        if (id == "") {

        } else {

            if (ConnectionManager().checkConnectivity(activity as Context)) {
                if (true) {

                    activity_cart_Progressdialog.visibility = View.VISIBLE
                    try {
                        val registerUser = JSONObject()
                        registerUser.put("token", id)

                        val queue = Volley.newRequestQueue(activity as Context)
                        val url = ("${resources.getString(R.string.url)}cart.php?method=getall")


                        val jsonObjectRequest = object : JsonObjectRequest(
                            Request.Method.POST,
                            url,
                            registerUser,

                            Response.Listener
                            {
                                val gson = Gson()
                                val result = gson.fromJson(it.toString(), LoginResult::class.java)

                                if (result.token != "" && result.message === null && result.status == 200) {
                                    val gson = Gson()
                                    cartListItems.clear()

                                    totalAmount = 0.0
                                    val result =
                                        gson.fromJson(
                                            it.getString("records"),
                                            Array<CartModel>::class.java
                                        )
                                    if (result.size > 0) {
                                        cartListItems.addAll(result)

                                        for (cm in cartListItems) {
                                            totalAmount += cm.Price * cm.Quantity
                                        }

                                        menuAdapter = CartAdapter(
                                            contextParam,
                                            cartListItems, this,fm
                                        )

                                        recyclerView.adapter =
                                            menuAdapter

                                        recyclerView.layoutManager =
                                            layoutManager 

                                        buttonPlaceOrder.text = "Заказать (" + totalAmount + " ₽.)"
                                    } else {
                                        buttonPlaceOrder.visibility = View.INVISIBLE

                                        cartListItems.clear()
                                        menuAdapter = CartAdapter(
                                            contextParam,
                                            cartListItems, this,fm
                                        )

                                        recyclerView.adapter =
                                            menuAdapter

                                        recyclerView.layoutManager =
                                            layoutManager
                                    }

                                    activity_cart_Progressdialog.visibility = View.INVISIBLE

                                } else if (result.token == null && result.message != null && result.status == 200) {

                                    Toast.makeText(
                                        contextParam,
                                        result.message,
                                        Toast.LENGTH_SHORT
                                    ).show()
                                }
                            },
                            Response.ErrorListener {
                                println("Error12 is " + it)
                                activity_cart_Progressdialog.visibility = View.INVISIBLE
                                println(it)
                                Toast.makeText(
                                    contextParam,
                                    "Some Error occurred!!!",
                                    Toast.LENGTH_SHORT
                                ).show()


                            }) {
                            override fun getHeaders(): MutableMap<String, String> {
                                val headers = HashMap<String, String>()

                                headers["Content-type"] = "application/json"
                                return headers
                            }
                        }

                        queue.add(jsonObjectRequest)

                    } catch (e: JSONException) {
                        Toast.makeText(
                            contextParam,
                            "Some unexpected error occured!!!",
                            Toast.LENGTH_SHORT
                        ).show()
                    }
                }
            } else {
                val alterDialog = androidx.appcompat.app.AlertDialog.Builder(activity as Context)

                alterDialog.setTitle("No Internet")
                alterDialog.setMessage("Internet Connection can't be establish!")
                alterDialog.setPositiveButton("Open Settings") { text, listener ->
                    val settingsIntent = Intent(Settings.ACTION_SETTINGS)
                    startActivity(settingsIntent)

                }

                alterDialog.setNegativeButton("Exit") { text, listener ->
                    ActivityCompat.finishAffinity(activity as Activity)
                }
                alterDialog.create()
                alterDialog.show()

            }
        }
    }
// onResume: Метод жизненного цикла фрагмента, вызываемый при восстановлении фрагмента после приостановки. Здесь выполняется проверка наличия интернет-соединения и обновление данных корзины, если соединение доступно.
    override fun onResume() {

        if (!ConnectionManager().checkConnectivity(activity as Context)) {

            val alterDialog = androidx.appcompat.app.AlertDialog.Builder(activity as Context)
            alterDialog.setTitle("No Internet")
            alterDialog.setMessage("Internet Connection can't be establish!")
            alterDialog.setPositiveButton("Open Settings") { text, listener ->
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)
                startActivity(settingsIntent)
            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(activity as Activity)
            }
            alterDialog.setCancelable(false)

            alterDialog.create()
            alterDialog.show()

        } else {
            fetchData()
        }

        super.onResume()
    }
}
