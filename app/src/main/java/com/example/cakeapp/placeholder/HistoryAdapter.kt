// Код, отвечающий за отображение истории заказов
package com.example.cakeapp.placeholder

import android.content.Context
import android.graphics.Color
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageButton
import android.widget.LinearLayout
import android.widget.TextView
import androidx.fragment.app.FragmentManager
import androidx.recyclerview.widget.RecyclerView
import com.example.cakeapp.R
import com.example.cakeapp.data.model.Order
import com.example.cakeapp.history_cart
import java.text.SimpleDateFormat
// Инициализация адаптера: Класс HistoryAdapter инициализируется с параметрами: контекстом (context), списком заказов (orderItems) и менеджером фрагментов (fm).
class HistoryAdapter(
    val context: Context,
    val orderItems: ArrayList<Order>,
    val fm: FragmentManager
) :
    RecyclerView.Adapter<HistoryAdapter.ViewHolderHistory>() {
// ViewHolder: Внутренний класс ViewHolderHistory используется для хранения ссылок на виджеты элемента инициируется из разметки history_item.xml.
    class ViewHolderHistory(view: View) : RecyclerView.ViewHolder(view) {
        val llHistory: LinearLayout = view.findViewById(R.id.llHistory)
        val txtOrderAddress: TextView = view.findViewById(R.id.txtOrderAddress)
        val txtHistoryDate: TextView = view.findViewById(R.id.txtHistoryDate)
        val txtHistoryStatus: TextView = view.findViewById(R.id.txtHistoryStatus)
        val txtHistoryDriver: TextView = view.findViewById(R.id.txtHistoryDriver)
        val txtHistoryPrice: TextView = view.findViewById(R.id.txtHistoryPrice)
        val btnOpenOrder: ImageButton = view.findViewById(R.id.btnOpenOrder)
    }
// onCreateViewHolder: Метод создает новый экземпляр ViewHolder при необходимости. Он надувает макет history_item для элемента списка.
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolderHistory {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.history_item, parent, false)

        return ViewHolderHistory(view)
    }
// getItemCount: Определяет количество элементов в списке заказов.
    override fun getItemCount(): Int {
        return orderItems.size
    }
// onBindViewHolder: Метод связывает данные заказа с виджетами в ViewHolder. Он устанавливает значения текста и цвета фона в соответствии с данными заказа. Также устанавливает обработчик нажатия кнопки для открытия заказа.
    override fun onBindViewHolder(holder: ViewHolderHistory, position: Int) {
        val orderItemObject = orderItems[position]
        holder.txtOrderAddress.text = orderItemObject.Address

        val sp = SimpleDateFormat("dd.MM.yyyy HH:mm:ss")
        holder.txtHistoryDate.text = sp.format(orderItemObject.CreationDate).toString()
        if (orderItemObject.Status == 0) {
            holder.txtHistoryStatus.text =context.resources.getString(R.string.waiting)
            holder.llHistory.setBackgroundColor(Color.parseColor("#a594f9"))
        } else if (orderItemObject.Status == 1) {
            holder.txtHistoryStatus.text = context.resources.getString(R.string.preparing)
            holder.llHistory.setBackgroundColor(Color.parseColor("#ffd97d"))
        } else if (orderItemObject.Status == 2) {
            holder.txtHistoryStatus.text = context.resources.getString(R.string.sent)
            holder.llHistory.setBackgroundColor(Color.parseColor("#caffbf"))
        } else if (orderItemObject.Status == 3) {
            holder.txtHistoryStatus.text = context.resources.getString(R.string.delivered)
            holder.llHistory.setBackgroundColor(Color.parseColor("#60d394"))
        } else if (orderItemObject.Status == 4) {
            holder.txtHistoryStatus.text = context.resources.getString(R.string.cancelled)
            holder.llHistory.setBackgroundColor(Color.parseColor("#ee6055"))
        }
        holder.txtHistoryDriver.text = orderItemObject.Driver
        holder.txtHistoryPrice.text = orderItemObject.Price.toString() + " ₽"
// Обработчик нажатия кнопки: При нажатии на кнопку открывается новый фрагмент history_cart, передавая необходимые данные о заказе (ID, адрес, цена) в качестве аргументов.
        holder.btnOpenOrder.setOnClickListener {
            fm.beginTransaction().replace(
                R.id.flFragment,
                history_cart(context, orderItemObject.ID, orderItemObject.Address, fm,orderItemObject.Price)
            ).commit()
        }
    }
}
