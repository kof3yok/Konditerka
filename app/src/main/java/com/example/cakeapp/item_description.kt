// Код, отображающий страницу с описанием товара, его составом и КБЖУ
package com.example.cakeapp

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
// Определение класса item_description: Это класс, который наследуется от класса Fragment, используемого в Android для создания интерфейсов пользователя. Класс принимает один параметр text в качестве описания элемента.
class item_description(val text: String) : Fragment() {
// Переопределение метода onCreateView: В этом методе создается и настраивается пользовательский интерфейс фрагмента.
// Создается экземпляр представления (view) с помощью метода inflate из указанного макета (fragment_item_description).
// Получается ссылка на текстовое поле txtDescription из макета.
// Устанавливается текст описания элемента в это текстовое поле.
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {

        var view = inflater.inflate(R.layout.fragment_item_description, container, false)
        val txtDescription: TextView = view.findViewById(R.id.txtDescription)
        txtDescription.text = text
        return view;
    }
// Компаньон объект: В Kotlin, companion object — это способ создания статических методов и переменных внутри класса. 
// Здесь компаньон объект содержит статический метод newInstance(), который возвращает новый экземпляр item_description с пустым описанием.
    companion object {
// Создание статического метода newInstance: Этот метод создает новый экземпляр item_description с пустым описанием. Он используется для создания новых экземпляров фрагмента.
        fun newInstance(): item_description {
            return item_description("")
        }
    }
}
