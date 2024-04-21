// Код, отвечающий за прокрутку товаров на главной странице
package com.example.cakeapp.placeholder

import android.widget.Toast
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.R
import com.example.cakeapp.data.model.Product
import com.google.gson.Gson
import com.google.gson.JsonObject
import java.util.ArrayList
import java.util.HashMap
import android.os.Bundle
// Инициализация списка и карты элементов: Объект содержит две коллекции: ITEMS - список элементов PlaceholderItem и ITEM_MAP - карта, связывающая идентификатор элемента с самим элементом.
object PlaceholderContent {

    val ITEMS: MutableList<PlaceholderItem> = ArrayList()

    val ITEM_MAP: MutableMap<String, PlaceholderItem> = HashMap()
// Создание фиктивных данных: В блоке init происходит инициализация списка ITEMS с помощью цикла for, где для каждого значения i от 1 до COUNT (25 в данном случае) вызывается функция createPlaceholderItem(i) для создания элемента и добавления его в список.
    private val COUNT = 25

    init {
        for (i in 1..COUNT) {
            addItem(createPlaceholderItem(i))
        }
    }

// Добавление элемента в список и карту: Функция addItem добавляет переданный элемент в список ITEMS и карту ITEM_MAP. Это позволяет быстро находить элементы по их идентификатору.
    private fun addItem(item: PlaceholderItem) {
        ITEMS.add(item)
        ITEM_MAP.put(item.id, item)
    }
// Создание элемента-заглушки: Функция createPlaceholderItem создает и возвращает новый элемент PlaceholderItem. Этот элемент имеет идентификатор (генерируемый на основе позиции), описание, цену, вторую цену и ссылку на изображение.
    private fun createPlaceholderItem(position: Int): PlaceholderItem {
        return PlaceholderItem(
            position.toString(),
            "Item " + position,
            position.toDouble(),
            position.toDouble(),
            position.toString()
        )
    }
// Описание элемента PlaceholderItem: Вложенный класс PlaceholderItem представляет собой модель элемента-заглушки. Он содержит поля для идентификатора, описания, цены, второй цены и ссылки на изображение.
    data class PlaceholderItem(
        val id: String, val description: String, val price: Double,
        val price2: Double, val image: String
    ) {
    }
}
