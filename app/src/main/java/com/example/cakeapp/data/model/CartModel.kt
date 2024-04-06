package com.example.cakeapp.data.model

import java.util.Date

data class CartModel(
    val Image: String,
    val ID: String,
    val Catalog: String,
    val Name: String,
    val Description: String,
    val Ingredients: String,
    val NutritionalValue: String,
    val ProductID: String,
    val PriceID: String,
    val PriceName: String,
    var Price: Double,
    var Quantity: Int,
    val UserID: String,
    val CreationDate: Date
)