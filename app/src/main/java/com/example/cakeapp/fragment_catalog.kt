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
import android.widget.Toast
import androidx.core.app.ActivityCompat
import androidx.fragment.app.FragmentManager
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.data.model.Product
import com.example.cakeapp.utils.ConnectionManager
import com.google.gson.Gson
import org.json.JSONException
import org.json.JSONObject
import java.util.HashMap

class fragment_catalog(val contextParam: Context, val catalogID: String,val fm:FragmentManager) : Fragment() {
    private var columnCount = 2
    lateinit var recyclerView: RecyclerView
    lateinit var layoutManager: RecyclerView.LayoutManager
    var productList = arrayListOf<Product>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        arguments?.let {
            columnCount = it.getInt(ItemFragment.ARG_COLUMN_COUNT)
        }
    }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_item_list, container, false)
        layoutManager = GridLayoutManager(context, columnCount)
        recyclerView = view.findViewById(R.id.list)
        return view
    }

    fun getProducts() {
        if (ConnectionManager().checkConnectivity(activity as Context)) {
            try {
                val queue = Volley.newRequestQueue(activity as Context)
                val url =resources.getString(R.string.url)+"product.php?method=getallbycatalogid"
                val productData = JSONObject()

                productData.put("CatalogID", catalogID)

                val jsonObjectRequest = object : JsonObjectRequest(
                    Request.Method.POST,
                    url,
                    productData,
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
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)//open wifi settings
                startActivity(settingsIntent)
            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(activity as Activity)//closes all the instances of the app and the app closes completely
            }
            alterDialog.setCancelable(false)

            alterDialog.create()
            alterDialog.show()

        }
    }

    override fun onResume() {//once setting is opened to turn internet we again check for connection

        if (ConnectionManager().checkConnectivity(activity as Context)) {
            if (productList.isEmpty())//if no data is loaded previously load new data
                getProducts()//if internet is available fetch data
        } else {

            val alterDialog = androidx.appcompat.app.AlertDialog.Builder(activity as Context)
            alterDialog.setTitle("No Internet")
            alterDialog.setMessage("Internet Connection can't be establish!")
            alterDialog.setPositiveButton("Open Settings") { text, listener ->
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)//open wifi settings
                startActivity(settingsIntent)
            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(activity as Activity)//closes all the instances of the app and the app closes completely
            }
            alterDialog.setCancelable(false)

            alterDialog.create()
            alterDialog.show()

        }

        super.onResume()
    }

    companion object {
        /**
         * Use this factory method to create a new instance of
         * this fragment using the provided parameters.
         *
         * @param param1 Parameter 1.
         * @param param2 Parameter 2.
         * @return A new instance of fragment fragment_catalog.
         */
        // TODO: Rename and change types and number of parameters
//        @JvmStatic
//        fun newInstance(param1: String, param2: String) =
//            fragment_catalog().apply {
//                arguments = Bundle().apply {
//                    putString(ARG_PARAM1, param1)
//                    putString(ARG_PARAM2, param2)
//                }
//            }
    }
}