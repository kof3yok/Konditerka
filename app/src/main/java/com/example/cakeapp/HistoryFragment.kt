// Код, отвечающий за отображение содержимого в истории заказов
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
import android.widget.ImageButton
import android.widget.RelativeLayout
import android.widget.Toast
import androidx.core.app.ActivityCompat
import androidx.fragment.app.FragmentManager
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.data.model.DateDeserializer
import com.example.cakeapp.data.model.LoginResult
import com.example.cakeapp.data.model.Order
import com.example.cakeapp.placeholder.HistoryAdapter
import com.example.cakeapp.utils.ConnectionManager
import com.google.gson.Gson
import com.google.gson.GsonBuilder
import org.json.JSONException
import org.json.JSONObject
import java.util.Date
// Класс HistoryFragment: Определяет фрагмент, отображающий историю заказов.
class HistoryFragment(
    val contextParam: Context,
    val fm: FragmentManager
) : Fragment() {


    var orderList = arrayListOf<Order>()
    lateinit var activity_history_Progressdialog: RelativeLayout
    lateinit var layoutManager: RecyclerView.LayoutManager
    lateinit var recyclerView: RecyclerView
    lateinit var menuAdapter: HistoryAdapter
// Функция onCreateView: Создает и возвращает представление фрагмента, загружая макет из файла fragment_history.xml и инициализируя необходимые элементы пользовательского интерфейса.
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {

        val view = inflater.inflate(R.layout.fragment_history, container, false)

        activity_history_Progressdialog = view.findViewById(R.id.activity_history_Progressdialog)

        layoutManager = LinearLayoutManager(contextParam)

        recyclerView = view.findViewById(R.id.historyRecyclerViewCart)
        return view
    }
/* Функция fetchData: Выполняет запрос на сервер для получения данных о заказах пользователя. 
Если пользователь авторизован (имеется его идентификатор в SharedPreferences), то создается JSON-объект с этим идентификатором и отправляется на сервер. 
Полученные данные обрабатываются с использованием библиотеки Gson для преобразования JSON в объекты классов Kotlin.*/
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

                    activity_history_Progressdialog.visibility = View.VISIBLE
                    try {
                        val registerUser = JSONObject()
                        registerUser.put("UserID", id)
)

                        val queue = Volley.newRequestQueue(activity as Context)
                        val url =
                            ("${resources.getString(R.string.url)}userorder.php?method=getall")

                        val jsonObjectRequest = object : JsonObjectRequest(
                            Request.Method.POST,
                            url,
                            registerUser,

                            Response.Listener
                            {
                                val gson = Gson()
                                val result = gson.fromJson(it.toString(), LoginResult::class.java)

                                if (result.token != "" && result.message === null && result.status == 200) {
                                    val gson = GsonBuilder()
                                        .registerTypeAdapter(Date::class.java, DateDeserializer())
                                        .create()
                                    orderList.clear()

                                    val result =
                                        gson.fromJson(
                                            it.getString("records"),
                                            Array<Order>::class.java
                                        )
                                    if (result.size > 0) {
                                        orderList.addAll(result)
                                /* Обработка ответа от сервера: Обработчик ответа JSON от сервера проверяет статус ответа и, в случае успеха, обновляет список заказов на основе полученных данных. 
                                 Затем этот список связывается с адаптером HistoryAdapter для отображения в RecyclerView.*/
                                        menuAdapter = HistoryAdapter(
                                            contextParam,
                                            orderList,
                                            fm
                                        )
            
                                        recyclerView.adapter =
                                            menuAdapter

                                        recyclerView.layoutManager =
                                            layoutManager 

                                    } else {

                                        menuAdapter = HistoryAdapter(
                                            contextParam,
                                            orderList,
                                            fm
                                        )

                                        recyclerView.adapter =
                                            menuAdapter

                                        recyclerView.layoutManager =
                                            layoutManager
                                    }

                                    activity_history_Progressdialog.visibility = View.INVISIBLE

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
                                activity_history_Progressdialog.visibility = View.INVISIBLE
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
/* Функция onResume: Вызывается при возобновлении фрагмента и выполняет проверку наличия интернет-соединения. 
Если соединение отсутствует, отображается диалоговое окно с предложением открыть настройки или завершить приложение. 
В противном случае выполняется функция fetchData для получения данных о заказах.*/
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
