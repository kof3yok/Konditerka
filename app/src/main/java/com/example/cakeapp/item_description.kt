package com.example.cakeapp

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView

class item_description(val text: String) : Fragment() {

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        // Inflate the layout for this fragment
        var view = inflater.inflate(R.layout.fragment_item_description, container, false)
        val txtDescription: TextView = view.findViewById(R.id.txtDescription)
        txtDescription.text = text
        return view;
    }
    companion object {
        fun newInstance(): item_description {
            return item_description("")
        }
    }
}