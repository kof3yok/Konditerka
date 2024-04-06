package com.example.cakeapp.placeholder

import android.content.Context
import android.graphics.BitmapFactory
import android.util.Base64
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageButton
import android.widget.ImageView
import android.widget.TextView
import androidx.fragment.app.FragmentManager
import androidx.recyclerview.widget.RecyclerView
import com.example.cakeapp.R
import com.example.cakeapp.data.model.CartModel
import com.example.cakeapp.item_detail

class HistoryCartAdapter(
    val context: Context,
    val cartItems: ArrayList<CartModel>,
    val fm:FragmentManager
) :
    RecyclerView.Adapter<HistoryCartAdapter.ViewHolderCart>() {
    class ViewHolderCart(view: View) : RecyclerView.ViewHolder(view) {
        val textViewOrderItem: TextView = view.findViewById(R.id.textViewOrderItem)
        val textViewOrderItemPrice: TextView = view.findViewById(R.id.textViewOrderItemPrice)
        val textViewOrderItemTotalPrice: TextView =
            view.findViewById(R.id.textViewOrderItemTotalPrice)
        val image: ImageView = view.findViewById(R.id.imgCartImage)
        val quantity: TextView = view.findViewById(R.id.textViewOrderItemQuantity)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolderCart {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.history_cart_item, parent, false)

        return ViewHolderCart(view)
    }

    override fun getItemCount(): Int {
        return cartItems.size
    }

    override fun onBindViewHolder(holder: HistoryCartAdapter.ViewHolderCart, position: Int) {
        val cartItemObject = cartItems[position]

        holder.textViewOrderItem.text = cartItemObject.Name
        holder.textViewOrderItemPrice.text = cartItemObject.Price.toString() + " ₽"
        holder.textViewOrderItemTotalPrice.text =
            (cartItemObject.Price * cartItemObject.Quantity).toString() + " ₽"
        holder.quantity.setText(cartItemObject.Quantity.toString())


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

}