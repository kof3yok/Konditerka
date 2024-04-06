package com.example.cakeapp.data.model

import java.util.Date

class Order(
    val ID: String,
    val UserID: String,
    val Price: Double,
    val CreationDate: Date,
    val Status: Int,
    val Driver: String?,
    val Address: String?
)