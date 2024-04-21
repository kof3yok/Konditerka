// Код, отвечающий за отображение каждого товара на главной странице
package com.example.cakeapp

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.provider.Settings
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.core.app.ActivityCompat
import androidx.fragment.app.FragmentManager
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.data.model.Product
import com.example.cakeapp.placeholder.PlaceholderContent
import com.example.cakeapp.utils.ConnectionManager
import com.google.gson.Gson
import com.google.gson.JsonObject
import org.json.JSONException
import java.io.BufferedReader
import java.io.InputStreamReader
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL
import java.net.URLEncoder
import java.util.HashMap

class ItemFragment(val contextParam: Context,val fm:FragmentManager) : Fragment() {

    private var columnCount = 2
    lateinit var recyclerView: RecyclerView
    lateinit var layoutManager: RecyclerView.LayoutManager
    var productList = arrayListOf<Product>()
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        arguments?.let {
            columnCount = it.getInt(ARG_COLUMN_COUNT)
        }
    }
// onCreateView(): Этот метод вызывается для создания и настройки макета фрагмента. Внутри него устанавливается макет для списка продуктов и менеджер компоновки для RecyclerView.
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_item_list, container, false)
        layoutManager = GridLayoutManager(context, columnCount)
        recyclerView = view.findViewById(R.id.list)

        return view
    }
// getProducts(): Этот метод отвечает за получение списка продуктов с сервера. 
// Он использует Volley для выполнения запроса к серверу, получения списка продуктов в формате JSON, их парсинга с помощью библиотеки Gson, и затем заполнения RecyclerView адаптером с полученными данными.
    fun getProducts() {
        if (ConnectionManager().checkConnectivity(activity as Context)) {
            try {
                val queue = Volley.newRequestQueue(activity as Context)
                val url = resources.getString(R.string.url) + "hitproduct.php?method=getall"
                val jsonObjectRequest = object : JsonObjectRequest(
                    Request.Method.POST,
                    url,
                    null,
                    Response.Listener {
                        val gson = Gson()
                        val result =
                            gson.fromJson(it.getString("records"), Array<Product>::class.java)
                        productList.addAll(result)
                        val dashboardAdapter = MyItemRecyclerViewAdapter(
                            contextParam,
                            productList,fm
                        )
                        recyclerView.adapter = dashboardAdapter
                        recyclerView.layoutManager = layoutManager
                    },
                    Response.ErrorListener {
                        println("errrrror" + it)
                        Toast.makeText(
                            activity as Context,
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
                    activity as Context,
                    "Some Unexpected error occured!!!",
                    Toast.LENGTH_SHORT
                ).show()
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
            alterDialog.setCancelable(false)

            alterDialog.create()
            alterDialog.show()

        }
    }
// onResume(): Этот метод вызывается при возобновлении фрагмента. Внутри него проверяется наличие интернет-соединения, и если оно есть, вызывается метод getProducts(). 
// Если интернет-соединение отсутствует, выводится диалоговое окно с предложением открыть настройки или выйти из приложения.
    override fun onResume() {

        if (ConnectionManager().checkConnectivity(activity as Context)) {
            if (productList.isEmpty())
                getProducts()
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
            alterDialog.setCancelable(false)

            alterDialog.create()
            alterDialog.show()

        }

        super.onResume()
    }
// Companion object: Это внутренний объект фрагмента, содержащий константу ARG_COLUMN_COUNT, которая используется для указания количества столбцов в RecyclerView.
    companion object {

        const val ARG_COLUMN_COUNT = "column-count"

    }
}
