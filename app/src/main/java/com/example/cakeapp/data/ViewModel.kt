package com.example.cakeapp.data

class ViewModel {
    val btnUserClickListener: (() -> Unit)? = null;
    fun onButtonClick() {
        btnUserClickListener?.invoke();
    }
}