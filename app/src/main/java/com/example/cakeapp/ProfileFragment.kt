// Код, отображающий страницу пользователя
package com.example.cakeapp

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.provider.Settings
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.RelativeLayout
import android.widget.TextView
import android.widget.Toast
import androidx.core.app.ActivityCompat
import androidx.fragment.app.FragmentManager
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.data.model.LoginResult
import com.example.cakeapp.data.model.Product
import com.example.cakeapp.data.model.User
import com.example.cakeapp.ui.login.LoginActivity
import com.example.cakeapp.ui.login.RegisterFragment
import com.example.cakeapp.utils.ConnectionManager
import com.google.gson.Gson
import org.json.JSONException
import org.json.JSONObject
// Класс ProfileFragment: Этот класс является фрагментом, который отображает профиль пользователя.
class ProfileFragment(val contextParam: Context, val fm: FragmentManager) : Fragment() {
    private lateinit var etxtUsername: EditText
    private lateinit var etxtEmail: EditText
    private lateinit var etxtMobileNumber: EditText
    private lateinit var etxtDeliveryAddress: EditText
    private lateinit var etxtPassword: EditText
    private lateinit var etxtConfirmPassword: EditText
    private lateinit var btnRegister: Button
    private lateinit var btnLogout: TextView
    private lateinit var register_fragment_Progressdialog: RelativeLayout
// Метод onCreateView: В этом методе происходит настройка макета фрагмента, инициализация полей ввода, кнопок и других элементов пользовательского интерфейса. Также устанавливаются слушатели событий для кнопок.
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_profile, container, false)

        etxtUsername = view.findViewById(R.id.etxtUserName)
        etxtEmail = view.findViewById(R.id.etxtEmail)
        etxtMobileNumber = view.findViewById(R.id.etxtPhone)
        etxtDeliveryAddress = view.findViewById(R.id.etxtAddress)
        etxtPassword = view.findViewById(R.id.etxtPassword)
        etxtConfirmPassword = view.findViewById(R.id.etxtPassword2)
        btnRegister = view.findViewById(R.id.btnRegister)
        register_fragment_Progressdialog = view.findViewById(R.id.Progressdialog)
        btnLogout = view.findViewById(R.id.txtLogout)

        btnRegister.setOnClickListener(View.OnClickListener {
            updateUser()
        })
        btnLogout.setOnClickListener(View.OnClickListener {

            val sp = contextParam.getSharedPreferences(
                R.string.shared_preferences.toString(),
                Context.MODE_PRIVATE
            )
            val editor = sp.edit()
            editor.clear()
            editor.commit()
            openRegisterFragment()
        })
        getUser()
        return view
    }
// Метод openRegisterFragment: Этот метод открывает фрагмент регистрации пользователя, заменяя текущий фрагмент в контейнере.
    fun openRegisterFragment() {

        val transaction = fragmentManager?.beginTransaction()

        transaction?.replace(
            R.id.flFragment,
            LoginActivity(contextParam, fm)
        )

        transaction?.commit()
    }
// Метод getUser: Этот метод получает информацию о пользователе с сервера. Он отправляет запрос к API, чтобы получить данные о пользователе по его идентификатору. Полученные данные используются для заполнения полей профиля.
    fun getUser() {

        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )
        val id = sp.getString("user_id", "").toString()
        if (ConnectionManager().checkConnectivity(activity as Context)) {
            if (true) {

                register_fragment_Progressdialog.visibility = View.VISIBLE
                try {
                    val registerUser = JSONObject()
                    registerUser.put("token", id)

                    val queue = Volley.newRequestQueue(activity as Context)
                    val url = ("${resources.getString(R.string.url)}login.php?method=getbyid")


                    val jsonObjectRequest = object : JsonObjectRequest(
                        Request.Method.POST,
                        url,
                        registerUser,

                        Response.Listener
                        {
                            val gson = Gson()
                            val result = gson.fromJson(it.toString(), LoginResult::class.java)

                            if (result.token != "" && result.message === null && result.status == 200) {
                                val gson = Gson()

                                var userList = arrayListOf<User>()

                                val result =
                                    gson.fromJson(it.getString("records"), Array<User>::class.java)
                                userList.addAll(result)

                                etxtUsername.setText(userList[0].Username)
                                etxtMobileNumber.setText(userList[0].Phone)
                                etxtEmail.setText(userList[0].EMail)
                                etxtDeliveryAddress.setText(userList[0].Address)

                                register_fragment_Progressdialog.visibility = View.INVISIBLE

                            } else if (result.token == null && result.message != null && result.status == 200) {

                                Toast.makeText(
                                    contextParam,
                                    result.message,
                                    Toast.LENGTH_SHORT
                                ).show()
                            }
                        },
                        Response.ErrorListener {
                            println("Error12 is " + it)
                            register_fragment_Progressdialog.visibility = View.INVISIBLE
                            println(it)
                            Toast.makeText(
                                contextParam,
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

                    queue.add(jsonObjectRequest)

                } catch (e: JSONException) {
                    Toast.makeText(
                        contextParam,
                        "Some unexpected error occured!!!",
                        Toast.LENGTH_SHORT
                    ).show()
                }
            }
        } else {
            val alterDialog = androidx.appcompat.app.AlertDialog.Builder(activity as Context)

            alterDialog.setTitle("No Internet")
            alterDialog.setMessage("Internet Connection can't be establish!")
            alterDialog.setPositiveButton("Open Settings") { text, listener ->
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)
                startActivity(settingsIntent)

            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(activity as Activity)
            }
            alterDialog.create()
            alterDialog.show()

        }
    }
// Метод updateUser: Этот метод отправляет измененные данные пользователя на сервер для их обновления. Он также проверяет наличие подключения к интернету и наличие ошибок ввода данных перед отправкой запроса.
    fun updateUser() {

        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )
        val id = sp.getString("user_id", "").toString()
        if (ConnectionManager().checkConnectivity(activity as Context)) {
            if (checkForErrors()) {

                register_fragment_Progressdialog.visibility = View.VISIBLE
                try {
                    val registerUser = JSONObject()
                    registerUser.put("token", id)
                    registerUser.put("username", etxtUsername.text)
                    registerUser.put("phone", etxtMobileNumber.text)
                    registerUser.put("password", etxtPassword.text)
                    registerUser.put("address", etxtDeliveryAddress.text)
                    registerUser.put("email", etxtEmail.text)

                    val queue = Volley.newRequestQueue(activity as Context)
                    val url = ("${resources.getString(R.string.url)}login.php?method=update")


                    val jsonObjectRequest = object : JsonObjectRequest(
                        Request.Method.POST,
                        url,
                        registerUser,
                        Response.Listener {
                            val gson = Gson()
                            val result = gson.fromJson(it.toString(), LoginResult::class.java)

                            if (result.token != "" && result.message === null && result.status == 200) {

                                sp.edit().putString("user_id", result.token).commit()
                                sp.edit()
                                    .putString("user_address", etxtDeliveryAddress.text.toString())
                                    .commit()


                                userSuccessfullyRegistered()
                            } else if (result.token == null && result.message != null && result.status == 200) {

                                Toast.makeText(
                                    contextParam,
                                    result.message,
                                    Toast.LENGTH_SHORT
                                ).show()
                            }
                        },
                        Response.ErrorListener {
                            println("Error12 is " + it)
                            register_fragment_Progressdialog.visibility = View.INVISIBLE
                            println(it)
                            Toast.makeText(
                                contextParam,
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

                    queue.add(jsonObjectRequest)

                } catch (e: JSONException) {
                    Toast.makeText(
                        contextParam,
                        "Some unexpected error occured!!!",
                        Toast.LENGTH_SHORT
                    ).show()
                }
            }
        } else {
            val alterDialog = androidx.appcompat.app.AlertDialog.Builder(activity as Context)

            alterDialog.setTitle("No Internet")
            alterDialog.setMessage("Internet Connection can't be establish!")
            alterDialog.setPositiveButton("Open Settings") { text, listener ->
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)
                startActivity(settingsIntent)

            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(activity as Activity)
            }
            alterDialog.create()
            alterDialog.show()

        }
    }
// Метод userSuccessfullyRegistered: Этот метод открывает главный экран приложения после успешной регистрации или обновления профиля.
    fun userSuccessfullyRegistered() {
        openDashBoard()
    }
// Метод openDashBoard: Этот метод открывает главный экран приложения (DashboardActivity).
    fun openDashBoard() {

        val intent = Intent(activity as Context, MainActivity::class.java)
        startActivity(intent)
        getActivity()?.finish()

    }
// Метод checkForErrors: Этот метод проверяет наличие ошибок ввода данных при регистрации или обновлении профиля. Он проверяет заполнены ли все обязательные поля и совпадают ли пароли.
    fun checkForErrors(): Boolean {
        var errorPassCount = 0
        if (etxtUsername.text.isBlank()) {

            etxtUsername.setError(contextParam.resources.getString(R.string.field_missing))
        } else {
            errorPassCount++
        }

        if (etxtMobileNumber.text.isBlank()) {
            etxtMobileNumber.setError(contextParam.resources.getString(R.string.field_missing))
        } else {
            errorPassCount++
        }

        if (etxtEmail.text.isBlank()) {
            etxtEmail.setError(contextParam.resources.getString(R.string.field_missing))
        } else {
            errorPassCount++
        }

        if (etxtDeliveryAddress.text.isBlank()) {
            etxtDeliveryAddress.setError(contextParam.resources.getString(R.string.field_missing))
        } else {
            errorPassCount++
        }

        if (etxtPassword.text.isNotBlank() && etxtConfirmPassword.text.isNotBlank()) {
            if (etxtPassword.text.toString().toInt() == etxtConfirmPassword.text.toString()
                    .toInt()
            ) {
                errorPassCount++
            } else {
                etxtConfirmPassword.setError(contextParam.resources.getString(R.string.password_dont_match))
            }
        } else {
            errorPassCount++
            errorPassCount++
            errorPassCount++
        }

        if (errorPassCount == 7)
            return true
        else
            return false
    }
// Метод onResume: В этом методе проверяется наличие интернет-соединения при восстановлении фрагмента после паузы. 
// Если соединение отсутствует, отображается диалоговое окно с предложением открыть настройки соединения или выйти из приложения.
    override fun onResume() {

        if (!ConnectionManager().checkConnectivity(activity as Context)) {

            val alterDialog = androidx.appcompat.app.AlertDialog.Builder(activity as Context)
            alterDialog.setTitle("No Internet")
            alterDialog.setMessage("Internet Connection can't be establish!")
            alterDialog.setPositiveButton("Open Settings") { text, listener ->
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)//open wifi settings
                startActivity(settingsIntent)
            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(activity as Activity)
            }
            alterDialog.setCancelable(false)

            alterDialog.create()
            alterDialog.show()

        }

        super.onResume()
    }
}
