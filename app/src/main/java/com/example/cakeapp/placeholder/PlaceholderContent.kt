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

/**
 * Helper class for providing sample content for user interfaces created by
 * Android template wizards.
 *
 * TODO: Replace all uses of this class before publishing your app.
 */
object PlaceholderContent {

    /**
     * An array of sample (placeholder) items.
     */
    val ITEMS: MutableList<PlaceholderItem> = ArrayList()

    /**
     * A map of sample (placeholder) items, by ID.
     */
    val ITEM_MAP: MutableMap<String, PlaceholderItem> = HashMap()

    private val COUNT = 25

    init {
        // Add some sample items.
        for (i in 1..COUNT) {
            addItem(createPlaceholderItem(i))
        }
    }


    private fun addItem(item: PlaceholderItem) {
        ITEMS.add(item)
        ITEM_MAP.put(item.id, item)
    }

    private fun createPlaceholderItem(position: Int): PlaceholderItem {
        return PlaceholderItem(
            position.toString(),
            "Item " + position,
            position.toDouble(),
            position.toDouble(),
            position.toString()
        )
    }


    /**
     * A placeholder item representing a piece of content.
     */
    data class PlaceholderItem(
        val id: String, val description: String, val price: Double,
        val price2: Double, val image: String
    ) {
    }
}