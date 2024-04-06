package com.example.cakeapp.data.model

data class Product(
    val Image: String,
    val ID: String,
    val CatalogID: String,
    val Catalog: String,
    val Name: String,
    val Description: String,
    val Ingredients: String,
    val NutritionalValue: String,
    val Price1ID: String,
    val Price1: String,
    var Price1Double: Double,
    val Price2ID: String,
    val Price2: String,
    var Price2Double: Double,
)