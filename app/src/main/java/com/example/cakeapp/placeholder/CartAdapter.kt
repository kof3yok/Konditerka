// Код, отвечающий за операции в корзине
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
// Класс CartAdapter: Этот класс наследуется от RecyclerView.Adapter и отвечает за связь данных между источником данных (в данном случае ArrayList<CartModel>) и представлением на экране пользователя.
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
// ViewHolderCart: Этот класс представляет элемент RecyclerView. Он содержит ссылки на различные виджеты, такие как текстовые поля, кнопки и изображения, которые отображаются в элементе списка.
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
// onCreateViewHolder(): Этот метод вызывается, когда RecyclerView требуется новый ViewHolder для элемента. Он создает новый экземпляр ViewHolderCart, связывает его с макетом элемента списка и возвращает его.
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolderCart {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.cart_recycler_view_single_row, parent, false)
        return ViewHolderCart(view)
    }
// getItemCount(): Этот метод возвращает общее количество элементов в списке.
    override fun getItemCount(): Int {
        return cartItems.size
    }
// onBindViewHolder(): Этот метод вызывается, когда RecyclerView требуется привязать данные к ViewHolder. 
// Он заполняет содержимое элемента списка данными из cartItems и устанавливает обработчики событий для кнопок увеличения, уменьшения и удаления элементов из корзины.
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

        holder.btnDeleteCartProduct.setOnClickListener {
            deleteCart(cartItemObject.ID, cartItemObject.UserID, position)
        }
        val imageBytes = Base64.decode(cartItemObject.Image, Base64.DEFAULT)
        val decodedImage = BitmapFactory.decodeByteArray(imageBytes, 0, imageBytes.size)
        holder.image.setImageBitmap(decodedImage)
        holder.image.setOnClickListener {
            fm.beginTransaction().replace(
                R.id.flFragment,
                item_detail(context, cartItemObject.ProductID, fm)
            ).commit()
        }
    }
// deleteCart(): Этот метод выполняет удаление элемента из корзины. Он отправляет запрос на удаление на сервер и, при успешном ответе, удаляет элемент из cartItems.
    fun deleteCart(cartID: String, userID: String, position: Int) {

        if (ConnectionManager().checkConnectivity(context as Context)) {
            if (true) {

                activity_cart_Progressdialog.visibility = View.VISIBLE
                try {
                    val registerUser = JSONObject()
                    registerUser.put("id", cartID)
                    registerUser.put("userid", userID)

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
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)
            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(context as Activity)
            }
            alterDialog.create()
            alterDialog.show()

        }
    }
// updateCart(): Этот метод выполняет обновление количества элементов в корзине. Он отправляет запрос на сервер с новым количеством товара, а затем обновляет данные в cartItems.
    fun updateCart(cartID: String, quantity: Int) {

        if (ConnectionManager().checkConnectivity(context as Context)) {
            if (true) {

                activity_cart_Progressdialog.visibility = View.VISIBLE
                try {
                    val registerUser = JSONObject()
                    registerUser.put("id", cartID)
                    registerUser.put("quantity", quantity)

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
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)
            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(context as Activity)
            }
            alterDialog.create()
            alterDialog.show()

        }
    }
// MinMaxFilter(): Это внутренний класс, реализующий интерфейс InputFilter. Он ограничивает вводимое пользователем количество товара в корзине от минимального до максимального значения.
    inner class MinMaxFilter() : InputFilter {
        private var intMin: Int = 0
        private var intMax: Int = 0

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

        private fun isInRange(a: Int, b: Int, c: Int): Boolean {
            return if (b > a) c in a..b else c in b..a
        }
    }
}
