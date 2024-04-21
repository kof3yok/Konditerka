// Код, отображающий страницу с историей заказов
package com.example.cakeapp

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.provider.Settings
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.LinearLayout
import android.widget.RelativeLayout
import android.widget.TextView
import android.widget.Toast
import androidx.core.app.ActivityCompat
import androidx.fragment.app.FragmentManager
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.data.model.CartModel
import com.example.cakeapp.data.model.LoginResult
import com.example.cakeapp.placeholder.HistoryCartAdapter
import com.example.cakeapp.utils.ConnectionManager
import com.google.gson.Gson
import org.json.JSONException
import org.json.JSONObject
// Инициализация переменных и элементов интерфейса: 
// В методе onCreateView, переменные и элементы интерфейса инициализируются, такие как historyCartAddressFrom, historyCartLL, historyCartProgress, btnHistoryTotal, recyclerView и layoutManager. 
// Также устанавливается адрес заказа и текст для кнопки с общей суммой заказа.
class history_cart(
    val contextParam: Context,
    val orderID: String,
    val address: String?,
    val fm: FragmentManager,
    val price: Double
) :
    Fragment() {

    var cartListItems = arrayListOf<CartModel>()
    lateinit var historyCartAddressFrom: TextView
    lateinit var historyCartLL: LinearLayout
    lateinit var historyCartProgress: RelativeLayout

    lateinit var recyclerView: RecyclerView
    lateinit var layoutManager: RecyclerView.LayoutManager
    lateinit var menuAdapter: HistoryCartAdapter
    lateinit var btnHistoryTotal: Button

    var totalAmount = 0.0
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_history_cart, container, false)

        historyCartAddressFrom = view.findViewById(R.id.historyCartAddressFrom)
        historyCartLL = view.findViewById(R.id.historyCartLL)
        historyCartProgress = view.findViewById(R.id.historyCartProgress)
        btnHistoryTotal = view.findViewById(R.id.btnHistoryTotal)

        layoutManager = LinearLayoutManager(contextParam)

        recyclerView = view.findViewById(R.id.historyRecyclerViewCart)
        historyCartAddressFrom.text = address
        btnHistoryTotal.text = contextParam.resources.getString(R.string.total) + " $price ₽"
        return view
    }
// Загрузка данных о заказе: Метод fetchData() выполняет загрузку данных о заказе из сервера. 
// Для этого отправляется POST-запрос к серверу, содержащий ID заказа. После получения ответа от сервера данные обрабатываются и отображаются в RecyclerView.
    fun fetchData() {
// Обработка ответа от сервера: В случае успешного ответа от сервера данные обрабатываются с помощью Gson и отображаются в RecyclerView. В случае ошибки выводится сообщение об ошибке.
        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )
        val id = sp.getString("user_id", "").toString()
        if (id == "") {

        } else {

            if (ConnectionManager().checkConnectivity(activity as Context)) {
                if (true) {

                    historyCartProgress.visibility = View.VISIBLE
                    try {
                        val registerUser = JSONObject()
                        registerUser.put("OrderID", orderID)

                        val queue = Volley.newRequestQueue(activity as Context)
                        val url =
                            ("${resources.getString(R.string.url)}userorderdetail.php?method=getall")


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

                                        menuAdapter = HistoryCartAdapter(
                                            contextParam,
                                            cartListItems, fm
                                        )

                                        recyclerView.adapter =
                                            menuAdapter

                                        recyclerView.layoutManager =
                                            layoutManager 

                                    } else {

                                        cartListItems.clear()
                                        menuAdapter = HistoryCartAdapter(
                                            contextParam,
                                            cartListItems, fm
                                        )

                                        recyclerView.adapter =
                                            menuAdapter

                                        recyclerView.layoutManager =
                                            layoutManager
                                    }

                                    historyCartProgress.visibility = View.INVISIBLE

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
                                historyCartProgress.visibility = View.INVISIBLE
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
// Обработка onResume(): Метод onResume() выполняет проверку наличия интернет-соединения при возвращении на фрагмент. Если соединение отсутствует, выводится сообщение и предлагается открыть настройки или завершить приложение.
    override fun onResume() {
// Проверка подключения к интернету: Перед загрузкой данных происходит проверка подключения к интернету. Если подключение отсутствует, выводится диалоговое окно с предложением открыть настройки и позволяющее завершить приложение.
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
