package com.example.cakeapp.data.model

import java.util.Date

class OrderDetail (
    val ID: String,
    val OrderID: String,
    val ProductID: String,
    val Price: Double,
    val Image: String,
    val Catalog: String,
    val Name: String,
    val Description: String,
    val CreationDate:Date,
    val Quantity:Int
)