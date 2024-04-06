package com.example.cakeapp.placeholder

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.graphics.BitmapFactory
import android.provider.Settings
import android.text.InputFilter
import android.text.Spanned
import android.util.Base64
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ImageButton
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.RelativeLayout
import android.widget.TextView
import android.widget.Toast
import androidx.core.app.ActivityCompat
import androidx.fragment.app.FragmentManager
import androidx.recyclerview.widget.RecyclerView
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.R
import com.example.cakeapp.activity_cart_Progressdialog
import com.example.cakeapp.data.model.CartModel
import com.example.cakeapp.data.model.LoginResult
import com.example.cakeapp.item_detail
import com.example.cakeapp.utils.ConnectionManager
import com.google.gson.Gson
import org.json.JSONException
import org.json.JSONObject

class CartAdapter(
    val context: Context,
    val cartItems: ArrayList<CartModel>,
    val handler: CartAdapter.Callbacks,
    val fm:FragmentManager
) :
    RecyclerView.Adapter<CartAdapter.ViewHolderCart>() {
    interface Callbacks {
        fun Update()
    }

    var LostFocus = false

    class ViewHolderCart(view: View) : RecyclerView.ViewHolder(view) {
        val textViewOrderItem: TextView = view.findViewById(R.id.textViewOrderItem)
        val textViewOrderItemPrice: TextView = view.findViewById(R.id.textViewOrderItemPrice)
        val textViewOrderItemTotalPrice: TextView =
            view.findViewById(R.id.textViewOrderItemTotalPrice)
        val image: ImageView = view.findViewById(R.id.imgCartImage)
        val quantity: TextView = view.findViewById(R.id.textViewOrderItemQuantity)
        val btnDeleteCartProduct: ImageButton = view.findViewById(R.id.btnDeleteCartProduct)
        val btnCartDec: Button = view.findViewById(R.id.btnCartDec)
        val btnCartInc: Button = view.findViewById(R.id.btnCartInc)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolderCart {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.cart_recycler_view_single_row, parent, false)
        return ViewHolderCart(view)
    }

    override fun getItemCount(): Int {
        return cartItems.size
    }

    override fun onBindViewHolder(holder: ViewHolderCart, position: Int) {
        val cartItemObject = cartItems[position]
        holder.quantity.filters = arrayOf<InputFilter>(MinMaxFilter(0, 999))

        holder.textViewOrderItem.text = cartItemObject.Name
        holder.textViewOrderItemPrice.text = cartItemObject.Price.toString() + " ₽"
        holder.textViewOrderItemTotalPrice.text =
            (cartItemObject.Price * cartItemObject.Quantity).toString() + " ₽"
        holder.quantity.setText(cartItemObject.Quantity.toString())

        holder.btnCartDec.setOnClickListener {

            if (holder.quantity.text.toString() != "1") {
                var quantity = holder.quantity.text.toString().toInt()
                quantity--
                cartItemObject.Quantity = quantity

                holder.textViewOrderItemTotalPrice.text =
                    (cartItemObject.Price * cartItemObject.Quantity).toString() + " ₽"
                handler.Update()
                updateCart(cartItemObject.ID, cartItemObject.Quantity)
            }
        }
        holder.btnCartInc.setOnClickListener {

            var quantity = holder.quantity.text.toString().toInt()
            quantity++
            cartItemObject.Quantity = quantity

            holder.textViewOrderItemTotalPrice.text =
                (cartItemObject.Price * cartItemObject.Quantity).toString() + " ₽"
            handler.Update()
            updateCart(cartItemObject.ID, cartItemObject.Quantity)

        }
//        holder.quantity.addTextChangedListener(object : TextWatcher {
//            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {
//                // Metin değişmeden önce
//            }
//
//            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
//                if (s.toString() != "") {
//
//                    if (holder.quantity.text.toString().toInt() == 0) {
//                        holder.quantity.setText("1")
//                        cartItemObject.Quantity = 1
//                    } else cartItemObject.Quantity = holder.quantity.text.toString().toInt()
//                    holder.textViewOrderItemTotalPrice.text =
//                        Math.round(cartItemObject.Price * cartItemObject.Quantity).toString()
//
//                    updateCart(cartItemObject.ID, cartItemObject.Quantity)
//                } else {
//                    // Metin değiştiği anda
////                    holder.quantity.setText("1")
////                    cartItemObject.Quantity = 1
////                    holder.textViewOrderItemTotalPrice.text =
////                        (cartItemObject.Price * cartItemObject.Quantity).toString()
//                    // Burada yapmak istediğiniz işlemleri gerçekleştirebilirsiniz
//
//                }
//            }
//
//            override fun afterTextChanged(s: Editable?) {
//                // Metin değiştikten sonra
//            }
//        })

//        holder.quantity.setOnFocusChangeListener { view, hasFocus ->
//            if (!hasFocus) {
//                if (holder.quantity.text.toString() == "") {
//                    holder.quantity.setText("1")
//                    cartItemObject.Quantity = 1
//                    holder.textViewOrderItemTotalPrice.text =
//                        (cartItemObject.Price * cartItemObject.Quantity).toString()
//                } else if (holder.quantity.text.toString() == "0") {
//                    holder.quantity.setText("1")
//                    cartItemObject.Quantity = 1
//                    holder.textViewOrderItemTotalPrice.text =
//                        (cartItemObject.Price * cartItemObject.Quantity).toString()
//                }
//            }
//        }

        holder.btnDeleteCartProduct.setOnClickListener {
            deleteCart(cartItemObject.ID, cartItemObject.UserID, position)
        }
        val imageBytes = Base64.decode(cartItemObject.Image, Base64.DEFAULT)
        val decodedImage = BitmapFactory.decodeByteArray(imageBytes, 0, imageBytes.size)
        //Picasso.get().load(item.Image).into(holder.image)
        holder.image.setImageBitmap(decodedImage)
        holder.image.setOnClickListener {
            fm.beginTransaction().replace(
                R.id.flFragment,
                item_detail(context, cartItemObject.ProductID, fm)
            ).commit()
        }
    }

    fun deleteCart(cartID: String, userID: String, position: Int) {

        if (ConnectionManager().checkConnectivity(context as Context)) {
            if (true) {

                activity_cart_Progressdialog.visibility = View.VISIBLE
                try {
                    val registerUser = JSONObject()
                    registerUser.put("id", cartID)
                    registerUser.put("userid", userID)
//                    registerUser.put("phone", etxtMobileNumber.text)
//                    registerUser.put("password", etxtPassword.text)
//                    registerUser.put("address", etxtDeliveryAddress.text)
//                    registerUser.put("email", etxtEmail.text)

                    val queue = Volley.newRequestQueue(context as Context)
                    val url = ("${context.resources.getString(R.string.url)}cart.php?method=delete")


                    val jsonObjectRequest = object : JsonObjectRequest(
                        Request.Method.POST,
                        url,
                        registerUser,

                        Response.Listener
                        {
                            val gson = Gson()
                            val result = gson.fromJson(it.toString(), LoginResult::class.java)

                            if (result.token != "" && result.message === null && result.status == 200) {

                                cartItems.removeAt(position)
                                notifyDataSetChanged()
                                handler.Update()

                                activity_cart_Progressdialog.visibility = View.INVISIBLE

                            } else if (result.token == null && result.message != null && result.status == 200) {

                                Toast.makeText(
                                    context,
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
                                context,
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
                        context,
                        "Some unexpected error occured!!!",
                        Toast.LENGTH_SHORT
                    ).show()
                }
            }
        } else {
            val alterDialog = androidx.appcompat.app.AlertDialog.Builder(context as Context)

            alterDialog.setTitle("No Internet")
            alterDialog.setMessage("Internet Connection can't be establish!")
            alterDialog.setPositiveButton("Open Settings") { text, listener ->
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)//open wifi settings
            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(context as Activity)//closes all the instances of the app and the app closes completely
            }
            alterDialog.create()
            alterDialog.show()

        }
    }

    fun updateCart(cartID: String, quantity: Int) {

        if (ConnectionManager().checkConnectivity(context as Context)) {
            if (true) {

                activity_cart_Progressdialog.visibility = View.VISIBLE
                try {
                    val registerUser = JSONObject()
                    registerUser.put("id", cartID)
                    registerUser.put("quantity", quantity)
//                    registerUser.put("phone", etxtMobileNumber.text)
//                    registerUser.put("password", etxtPassword.text)
//                    registerUser.put("address", etxtDeliveryAddress.text)
//                    registerUser.put("email", etxtEmail.text)

                    val queue = Volley.newRequestQueue(context as Context)
                    val url = ("${context.resources.getString(R.string.url)}cart.php?method=update")


                    val jsonObjectRequest = object : JsonObjectRequest(
                        Request.Method.POST,
                        url,
                        registerUser,

                        Response.Listener
                        {
                            val gson = Gson()
                            val result = gson.fromJson(it.toString(), LoginResult::class.java)

                            if (result.token != "" && result.message === null && result.status == 200) {
                                activity_cart_Progressdialog.visibility = View.INVISIBLE
                                notifyDataSetChanged()
                                handler.Update()
                            } else if (result.token == null && result.message != null && result.status == 200) {

                                Toast.makeText(
                                    context,
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
                                context,
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
                        context,
                        "Some unexpected error occured!!!",
                        Toast.LENGTH_SHORT
                    ).show()
                }
            }
        } else {
            val alterDialog = androidx.appcompat.app.AlertDialog.Builder(context as Context)

            alterDialog.setTitle("No Internet")
            alterDialog.setMessage("Internet Connection can't be establish!")
            alterDialog.setPositiveButton("Open Settings") { text, listener ->
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)//open wifi settings
            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(context as Activity)//closes all the instances of the app and the app closes completely
            }
            alterDialog.create()
            alterDialog.show()

        }
    }

    inner class MinMaxFilter() : InputFilter {
        private var intMin: Int = 0
        private var intMax: Int = 0

        // Initialized
        constructor(minValue: Int, maxValue: Int) : this() {
            this.intMin = minValue
            this.intMax = maxValue
        }

        override fun filter(
            source: CharSequence,
            start: Int,
            end: Int,
            dest: Spanned,
            dStart: Int,
            dEnd: Int
        ): CharSequence? {
            try {
                val input = Integer.parseInt(dest.toString() + source.toString())
                if (isInRange(intMin, intMax, input)) {
                    return null
                }
            } catch (e: NumberFormatException) {
                e.printStackTrace()
            }
            return ""
        }

        // Check if input c is in between min a and max b and
        // returns corresponding boolean
        private fun isInRange(a: Int, b: Int, c: Int): Boolean {
            return if (b > a) c in a..b else c in b..a
        }
    }
}