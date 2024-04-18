// Код, отвечающий за прокрутку товаров на главной странице
package com.example.cakeapp

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.graphics.BitmapFactory
import android.provider.Settings
import android.util.Base64
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import android.widget.Toast
import androidx.compose.ui.res.stringResource
import androidx.core.app.ActivityCompat
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.data.model.Product

import com.example.cakeapp.placeholder.PlaceholderContent.PlaceholderItem
import com.example.cakeapp.databinding.FragmentItemBinding
import com.example.cakeapp.ui.login.LoginActivity
import com.example.cakeapp.utils.ConnectionManager
import com.google.android.material.button.MaterialButton
import com.google.gson.Gson
import org.json.JSONException
import org.json.JSONObject
import java.util.HashMap

/**
 * [RecyclerView.Adapter] that can display a [PlaceholderItem].
 * TODO: Replace the implementation with code for your data type.
 */
class MyItemRecyclerViewAdapter(
    val context: Context,
    private val values: List<Product>,
    val fm: FragmentManager
) : RecyclerView.Adapter<MyItemRecyclerViewAdapter.ViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {

        return ViewHolder(
            FragmentItemBinding.inflate(
                LayoutInflater.from(parent.context),
                parent,
                false
            )
        )

    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val item = values[position]
        holder.image.setOnClickListener {
            fm.beginTransaction().replace(
                R.id.flFragment,
                item_detail(context, item.ID, fm)
            ).commit()
        }

        holder.price.setOnClickListener {
            val bd = holder.price.getTag() as ArrayList<String>
            holder.add.text = "Добавить " + bd[0].toString()
            val buttonData = holder.add.getTag() as ArrayList<String>
            buttonData[0] = bd[0].toString()
            buttonData[1] = bd[1].toString()
            holder.add.setTag(buttonData)
            holder.price.background = context.resources.getDrawable(R.color.green)
            holder.price2.background = context.resources.getDrawable(R.color.transparent)
        }
        holder.price2.setOnClickListener {
            val bd = holder.price2.getTag() as ArrayList<String>
            holder.add.text = "Добавить " + bd[0].toString()
            val buttonData = holder.add.getTag() as ArrayList<String>
            buttonData[0] = bd[0].toString()
            buttonData[1] = bd[1].toString()
            holder.add.setTag(buttonData)
            holder.price2.background = context.resources.getDrawable(R.color.green)
            holder.price.background = context.resources.getDrawable(R.color.transparent)
        }
        holder.description.text = item.Name
        holder.description.setTag(item.ID)

        holder.price.visibility = View.VISIBLE
        holder.price2.visibility = View.VISIBLE
        holder.add.visibility = View.VISIBLE
        holder.add.setOnClickListener {
            this.addBasket(it as MaterialButton)
        }
        if (item.Price1.isNullOrEmpty()) {
            holder.price.visibility = View.INVISIBLE
            holder.add.visibility = View.INVISIBLE
        } else {
            val vArr = item.Price1.split(";")
            if (vArr.size > 2) {
                item.Price1Double = vArr[2].toDouble()

                if (item.Price1Double == 0.0) {
                    holder.price.visibility = View.INVISIBLE
                    holder.add.visibility = View.INVISIBLE
                } else {
                    holder.price.text = vArr[0]
                    holder.price.background = context.resources.getDrawable(R.color.green)

                    val buttonData = ArrayList<String>()
                    buttonData.add(item.Price1Double.toString())
                    buttonData.add(item.Price1ID)
                    buttonData.add(item.ID)
                    val copiedList = buttonData.map { it }.toMutableList()
                    holder.add.setTag(copiedList)
                    holder.price.setTag(buttonData)
                    holder.add.text = "Добавить " + item.Price1Double.toString()
                }
            } else {
                holder.add.visibility = View.INVISIBLE
            }
        }

        if (item.Price2.isNullOrEmpty()) {
            holder.price2.visibility = View.INVISIBLE
            holder.add.visibility = View.INVISIBLE
        } else {
            val vArr = item.Price2.split(";")
            if (vArr.size > 2) {
                item.Price2Double = vArr[2].toDouble()

                if (item.Price2Double == 0.0) {
                    holder.price2.visibility = View.INVISIBLE
                    holder.add.visibility = View.INVISIBLE
                } else {
                    holder.price2.text = vArr[0]
                    val buttonData = ArrayList<String>()
                    buttonData.add(item.Price2Double.toString())
                    buttonData.add(item.Price2ID)
                    buttonData.add(item.ID)
                    holder.price2.setTag(buttonData)
                }
            } else {
                holder.add.visibility = View.INVISIBLE
            }
        }
        if (item.Image != null && item.Image.length > 0) {
            val imageBytes = Base64.decode(item.Image, Base64.DEFAULT)
            val decodedImage = BitmapFactory.decodeByteArray(imageBytes, 0, imageBytes.size)
            //Picasso.get().load(item.Image).into(holder.image)
            holder.image.setImageBitmap(decodedImage)
        }
    }

    fun addBasket(btn: MaterialButton) {
        val sp = context.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )
        val id = sp.getString("user_id", "").toString()
        if (id == "") {
            Toast.makeText(
                context,
                "Войдите в аккаунт, чтобы сделать заказ.",
                Toast.LENGTH_SHORT
            ).show()
        } else {


            val bd = btn.getTag() as ArrayList<String>
            if (ConnectionManager().checkConnectivity(context as Context)) {
                try {
                    val queue = Volley.newRequestQueue(context as Context)

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
                                context as Context,
                                "ДОБАВЛЕН!",
                                Toast.LENGTH_SHORT
                            ).show()
                        },
                        Response.ErrorListener {
                            println("errrrror" + it)
                            Toast.makeText(
                                context as Context,
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

    override fun getItemCount(): Int = values.size

    inner class ViewHolder(binding: FragmentItemBinding) : RecyclerView.ViewHolder(binding.root) {
        val description: TextView = binding.txtDescription
        val price: TextView = binding.txtPrice
        val price2: TextView = binding.txtPrice2
        val add: Button = binding.btnAddCart
        val image: ImageView = binding.imgImage

        override fun toString(): String {
            return super.toString() + " '" + description.text + "'₽"
        }
    }

}
