// Код, отвечающий за отображение главной страницы приложения после входа
package com.example.cakeapp

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.provider.Settings
import android.view.Menu
import android.view.MenuItem
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.view.GravityCompat
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import com.android.volley.Response
import com.android.volley.toolbox.StringRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.data.model.Catalog
import com.example.cakeapp.databinding.ActivityMainBinding
import com.example.cakeapp.ui.login.LoginActivity
import com.example.cakeapp.utils.ConnectionManager
import com.google.android.material.navigation.NavigationView.OnNavigationItemSelectedListener
import com.google.gson.Gson
import org.json.JSONException
import org.json.JSONObject
// Навигация и управление фрагментами: Класс MainActivity является главной активностью приложения. 
//В методе onCreate() настраивается интерфейс активности, устанавливаются слушатели для элементов навигации (bottomNavigationView и nvMenu) и фрагменты, связанные с каждым пунктом нижней навигационной панели, открываются при соответствующих действиях пользователя.
class MainActivity : AppCompatActivity(), OnNavigationItemSelectedListener {

    private lateinit var fragmentManager: FragmentManager
    private lateinit var binding: ActivityMainBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        binding.bottomNavigationView.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.Profile -> openFragment(LoginActivity(this, fragmentManager))
                R.id.Home -> openFragment(ItemFragment(this, fragmentManager))
                R.id.History -> openFragment(HistoryFragment(this, fragmentManager))
                R.id.Cart -> {
                    val sp = this.getSharedPreferences(
                        R.string.shared_preferences.toString(),
                        Context.MODE_PRIVATE
                    )
                    val id = sp.getString("user_id", "").toString()
                    if (id == "") {
                        Toast.makeText(
                            this,
                            "Войдите в аккаунт, чтобы сделать заказ.",
                            Toast.LENGTH_SHORT
                        ).show()
                        openFragment(LoginActivity(this,fragmentManager))
                    } else openFragment(CartActivity(this, fragmentManager))
                }

                R.id.Menu -> {
                    if (binding.dl.isDrawerOpen(GravityCompat.START))
                        binding.dl.closeDrawer(GravityCompat.START)
                    binding.dl.openDrawer(GravityCompat.START)
                }
            }
            true
        }
        getCatalog()
        binding.nvMenu.setNavigationItemSelectedListener(this)

        fragmentManager = supportFragmentManager
        openFragment(ItemFragment(this, fragmentManager))
        binding.bottomNavigationView.selectedItemId = R.id.Home
    }
/*Получение каталога товаров: Метод getCatalog() отправляет запрос на сервер для получения списка товаров. Если устройство подключено к Интернету, используется библиотека Volley для выполнения запроса. 
Полученные данные обрабатываются и используются для динамического создания пунктов меню (nvMenu). 
Если соединение с Интернетом отсутствует, выводится сообщение об ошибке и предлагается открыть настройки сети.*/
    fun getCatalog() {
        val catalogList = arrayListOf<Catalog>()
        if (ConnectionManager().checkConnectivity(this)) {
            try {
                val queue = Volley.newRequestQueue(this)
                val url = resources.getString(R.string.url) + "catalog.php?method=getall"

                val req: StringRequest = object : StringRequest(
                    Method.POST, url,
                    Response.Listener {
                        val res = JSONObject(it)
                        val gson = Gson()
                        val result =
                            gson.fromJson(res.getString("records"), Array<Catalog>::class.java)
                        catalogList.addAll(result)

                        binding.nvMenu.menu.clear()
                        var idd = binding.nvMenu.id
                        catalogList.forEach {
                            binding.nvMenu.menu.add(
                                0,
                                ++idd,
                                Menu.NONE,
                                it.Name
                            ).tooltipText = it.ID
                        }
                    },
                    Response.ErrorListener {
                        println(it)
                        Toast.makeText(
                            this,
                            "Some Error occurred!!!",
                            Toast.LENGTH_SHORT
                        ).show()
                    }) {
                    override fun getHeaders(): MutableMap<String, String> {
                        val headers = HashMap<String, String>()
                        headers["Content-type"] = "application/json"
                        return headers
                    }
                }
                queue.add(req)

            } catch (e: JSONException) {
                Toast.makeText(
                    this,
                    "Some Unexpected error occured!!!",
                    Toast.LENGTH_SHORT
                ).show()
            }
        } else {

            val alterDialog = androidx.appcompat.app.AlertDialog.Builder(this)
            alterDialog.setTitle("No Internet")
            alterDialog.setMessage("Internet Connection can't be establish!")
            alterDialog.setPositiveButton("Open Settings") { text, listener ->
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)//open wifi settings
                startActivity(settingsIntent)
            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(this)
            }
            alterDialog.setCancelable(false)

            alterDialog.create()
            alterDialog.show()

        }
    }
/*Обработка нажатия кнопки "назад": Метод onBackPressed() перехватывает событие нажатия кнопки "назад" на устройстве. 
Если боковое меню (dl) открыто, оно закрывается. В противном случае выполняется обычное поведение кнопки "назад".*/
    override fun onBackPressed() {
        if (binding.dl.isDrawerOpen(GravityCompat.START)) {
            binding.dl.closeDrawer(GravityCompat.START)
        } else {
            super.onBackPressedDispatcher.onBackPressed()
        }
        super.onBackPressed()
    }

    private fun openFragment(fragment: Fragment) {

        fragmentManager.beginTransaction().replace(R.id.flFragment, fragment).commit()
    }
// Открытие фрагмента при навигации по боковому меню: Метод onNavigationItemSelected() отвечает за открытие соответствующего фрагмента при выборе пункта из бокового меню.
    override fun onNavigationItemSelected(item: MenuItem): Boolean {
        openFragment(fragment_catalog(this, item.tooltipText.toString(), fragmentManager))
        binding.dl.closeDrawer(GravityCompat.START)
        return true
    }
}
