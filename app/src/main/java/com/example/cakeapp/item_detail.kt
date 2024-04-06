package com.example.cakeapp

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.graphics.BitmapFactory
import android.os.Bundle
import android.provider.Settings
import android.util.Base64
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import android.widget.Toast
import androidx.core.app.ActivityCompat
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import androidx.viewpager.widget.PagerAdapter
import androidx.viewpager.widget.ViewPager
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.data.model.Product
import com.example.cakeapp.placeholder.ViewPagerAdapter
import com.example.cakeapp.utils.ConnectionManager
import com.google.android.material.button.MaterialButton
import com.google.android.material.tabs.TabLayout
import com.google.gson.Gson
import org.json.JSONException
import org.json.JSONObject

class item_detail(
    val contextParam: Context,
    val productID: String,
    val fm:FragmentManager
) : Fragment() {
    lateinit var imgProduct: ImageView
    lateinit var txtProductName: TextView
    lateinit var txtPrice: TextView
    lateinit var txtPrice2: TextView
    lateinit var btnAddCart: Button
    lateinit var tabLayoutProduct: TabLayout
    lateinit var product: Product
    lateinit var pager: ViewPager
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_item_detail, container, false)
        imgProduct = view.findViewById(R.id.imgProduct)
        txtProductName = view.findViewById(R.id.txtProductName)
        txtPrice = view.findViewById(R.id.txtPrice)
        txtPrice2 = view.findViewById(R.id.txtPrice2)
        btnAddCart = view.findViewById(R.id.btnAddCart)
        tabLayoutProduct = view.findViewById(R.id.tabLayoutProduct)
        pager = view.findViewById(R.id.viewPager)

        getProduct()
        return view
    }

    fun getProduct() {
        if (ConnectionManager().checkConnectivity(activity as Context)) {
            try {
                val queue = Volley.newRequestQueue(activity as Context)
                val registerUser = JSONObject()
                registerUser.put("ProductID", productID)
                val url = resources.getString(R.string.url) + "product.php?method=getbyid"
                val jsonObjectRequest = object : JsonObjectRequest(
                    Request.Method.POST,
                    url,
                    registerUser,
                    Response.Listener {
                        val gson = Gson()
                        val result =
                            gson.fromJson(it.getString("records"), Array<Product>::class.java)
                        val productList = arrayListOf<Product>()

                        productList.addAll(result)
                        product = productList[0]

                        if (product.Image != null && product.Image.length > 0) {
                            val imageBytes = Base64.decode(product.Image, Base64.DEFAULT)
                            val decodedImage =
                                BitmapFactory.decodeByteArray(imageBytes, 0, imageBytes.size)
                            //Picasso.get().load(item.Image).into(holder.image)
                            imgProduct.setImageBitmap(decodedImage)
                        }
                        txtProductName.text = product.Name

                        val adapter = ViewPagerAdapter(fm)

                        adapter.addFragment(item_description(product.Description), "Описание")
                        adapter.addFragment(item_description(product.Ingredients), "Состав")
                        adapter.addFragment(item_description(product.NutritionalValue), "КБЖУ")

                        pager.adapter = adapter

                        tabLayoutProduct.setupWithViewPager(pager)


                        txtPrice.setOnClickListener {
                            val bd = txtPrice.getTag() as ArrayList<String>
                            btnAddCart.text = "Add Cart " + bd[0].toString()
                            val buttonData = btnAddCart.getTag() as ArrayList<String>
                            buttonData[0] = bd[0].toString()
                            buttonData[1] = bd[1].toString()
                            btnAddCart.setTag(buttonData)
                            txtPrice.background = contextParam.resources.getDrawable(R.color.green)
                            txtPrice2.background =
                                contextParam.resources.getDrawable(R.color.transparent)
                        }
                        txtPrice2.setOnClickListener {
                            val bd = txtPrice2.getTag() as ArrayList<String>
                            btnAddCart.text = "Добавить " + bd[0].toString()
                            val buttonData = btnAddCart.getTag() as ArrayList<String>
                            buttonData[0] = bd[0].toString()
                            buttonData[1] = bd[1].toString()
                            btnAddCart.setTag(buttonData)
                            txtPrice2.background = contextParam.resources.getDrawable(R.color.green)
                            txtPrice.background =
                                contextParam.resources.getDrawable(R.color.transparent)
                        }

                        txtPrice.visibility = View.VISIBLE
                        txtPrice2.visibility = View.VISIBLE
                        btnAddCart.visibility = View.VISIBLE
                        btnAddCart.setOnClickListener {
                            this.addBasket(it as MaterialButton)
                        }
                        if (product.Price1.isNullOrEmpty()) {
                            txtPrice.visibility = View.INVISIBLE
                            btnAddCart.visibility = View.INVISIBLE
                        } else {
                            val vArr = product.Price1.split(";")
                            if (vArr.size > 2) {
                                product.Price1Double = vArr[2].toDouble()

                                if (product.Price1Double == 0.0) {
                                    txtPrice.visibility = View.INVISIBLE
                                    btnAddCart.visibility = View.INVISIBLE
                                } else {
                                    txtPrice.text = vArr[0]
                                    txtPrice.background =
                                        contextParam.resources.getDrawable(R.color.green)

                                    val buttonData = ArrayList<String>()
                                    buttonData.add(product.Price1Double.toString())
                                    buttonData.add(product.Price1ID)
                                    buttonData.add(product.ID)
                                    val copiedList = buttonData.map { it }.toMutableList()
                                    btnAddCart.setTag(copiedList)
                                    txtPrice.setTag(buttonData)
                                    btnAddCart.text = "Добавить " + product.Price1Double.toString()
                                }
                            } else {
                                btnAddCart.visibility = View.INVISIBLE
                            }
                        }

                        if (product.Price2.isNullOrEmpty()) {
                            txtPrice2.visibility = View.INVISIBLE
                            btnAddCart.visibility = View.INVISIBLE
                        } else {
                            val vArr = product.Price2.split(";")
                            if (vArr.size > 2) {
                                product.Price2Double = vArr[2].toDouble()

                                if (product.Price2Double == 0.0) {
                                    txtPrice2.visibility = View.INVISIBLE
                                    btnAddCart.visibility = View.INVISIBLE
                                } else {
                                    txtPrice2.text = vArr[0]
                                    val buttonData = ArrayList<String>()
                                    buttonData.add(product.Price2Double.toString())
                                    buttonData.add(product.Price2ID)
                                    buttonData.add(product.ID)
                                    txtPrice2.setTag(buttonData)
                                }
                            } else {
                                btnAddCart.visibility = View.INVISIBLE
                            }
                        }
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

    fun addBasket(btn: MaterialButton) {
        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )
        val id = sp.getString("user_id", "").toString()
        if (id == "") {
            Toast.makeText(
                contextParam,
                "Войдите в аккаунт, чтобы сделать заказ.",
                Toast.LENGTH_SHORT
            ).show()
        } else {


            val bd = btn.getTag() as ArrayList<String>
            if (ConnectionManager().checkConnectivity(contextParam as Context)) {
                try {
                    val queue = Volley.newRequestQueue(contextParam as Context)

                    val registerUser = JSONObject()
                    registerUser.put("userid", id)
                    registerUser.put("priceid", bd[1])
                    registerUser.put("quantity", 1)
                    val url = "http://185.86.15.144/ruslan/api/cart.php?method=create"
                    val jsonObjectRequest = object : JsonObjectRequest(
                        Request.Method.POST,
                        url,
                        registerUser,
                        Response.Listener {
                            val gson = Gson()
                            val result =
                                gson.fromJson(it.getString("records"), Array<Product>::class.java)

                            Toast.makeText(
                                contextParam as Context,
                                "ДОБАВЛЕН!",
                                Toast.LENGTH_SHORT
                            ).show()
                        },
                        Response.ErrorListener {
                            println("errrrror" + it)
                            Toast.makeText(
                                contextParam as Context,
                                "Some Error occurred!!!",
                                Toast.LENGTH_SHORT
                            ).show()
                        }) {
                        override fun getHeaders(): MutableMap<String, String> {
                            val headers = java.util.HashMap<String, String>()
                            headers["Content-type"] = "application/json"
                            return headers
                        }
                    }

                    queue.add(jsonObjectRequest)
                } catch (e: JSONException) {
                    Toast.makeText(
                        context as Context,
                        "Some Unexpected error occured!!!",
                        Toast.LENGTH_SHORT
                    ).show()
                }
            } else {

                val alterDialog = androidx.appcompat.app.AlertDialog.Builder(context as Context)
                alterDialog.setTitle("No Internet")
                alterDialog.setMessage("Internet Connection can't be establish!")
                alterDialog.setPositiveButton("Open Settings") { text, listener ->
                    val settingsIntent = Intent(Settings.ACTION_SETTINGS)//open wifi settings
                    //startActivity(settingsIntent)
                }

                alterDialog.setNegativeButton("Exit") { text, listener ->
                    ActivityCompat.finishAffinity(context as Activity)//closes all the instances of the app and the app closes completely
                }
                alterDialog.setCancelable(false)

                alterDialog.create()
                alterDialog.show()

            }
        }
    }
}