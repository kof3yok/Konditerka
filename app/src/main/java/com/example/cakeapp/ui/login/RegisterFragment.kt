// Код, отвечающий за выполнение необходимых функций при регистрации в приложение
package com.example.cakeapp.ui.login

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
import android.widget.Toast
import androidx.core.app.ActivityCompat
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.MainActivity
import com.example.cakeapp.R
import com.example.cakeapp.data.model.LoginResult
import com.example.cakeapp.utils.ConnectionManager
import com.google.gson.Gson
import org.json.JSONException
import org.json.JSONObject

class RegisterFragment(val contextParam: Context) : Fragment() {
    private lateinit var etxtUsername: EditText
    private lateinit var etxtEmail: EditText
    private lateinit var etxtMobileNumber: EditText
    private lateinit var etxtDeliveryAddress: EditText
    private lateinit var etxtPassword: EditText
    private lateinit var etxtConfirmPassword: EditText
    private lateinit var btnRegister: Button
    private lateinit var register_fragment_Progressdialog: RelativeLayout
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        // Inflate the layout for this fragment
        val view = inflater.inflate(R.layout.fragment_register, container, false)

        etxtUsername = view.findViewById(R.id.etxtUserName)
        etxtEmail = view.findViewById(R.id.etxtEmail)
        etxtMobileNumber = view.findViewById(R.id.etxtPhone)
        etxtDeliveryAddress = view.findViewById(R.id.etxtAddress)
        etxtPassword = view.findViewById(R.id.etxtPassword)
        etxtConfirmPassword = view.findViewById(R.id.etxtPassword2)
        btnRegister = view.findViewById(R.id.btnRegister)
        register_fragment_Progressdialog = view.findViewById(R.id.Progressdialog)

        btnRegister.setOnClickListener(View.OnClickListener {
            registerUser()
        })
        return view
    }

    fun registerUser() {

        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )
        sp.edit().putString("user_id", "").commit()
        if (ConnectionManager().checkConnectivity(activity as Context)) {
            if (checkForErrors()) {

                register_fragment_Progressdialog.visibility = View.VISIBLE
                try {
                    val registerUser = JSONObject()
                    registerUser.put("username", etxtUsername.text)
                    registerUser.put("phone", etxtMobileNumber.text)
                    registerUser.put("password", etxtPassword.text)
                    registerUser.put("address", etxtDeliveryAddress.text)
                    registerUser.put("email", etxtEmail.text)

                    val queue = Volley.newRequestQueue(activity as Context)
                    val url = resources.getString(R.string.url) + getString(R.string.Register)

                    val jsonObjectRequest = object : JsonObjectRequest(
                        Request.Method.POST,
                        url,
                        registerUser,
                        Response.Listener {
                            val gson = Gson()
                            val result = gson.fromJson(it.toString(), LoginResult::class.java)

                            if (result.token != "" && result.message === null && result.status == 200) {

                                sp.edit().putString("user_id", result.token).commit()

                                Toast.makeText(
                                    contextParam,
                                    "SUCCESS",
                                    Toast.LENGTH_SHORT
                                ).show()

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
                val settingsIntent = Intent(Settings.ACTION_SETTINGS)//open wifi settings
                startActivity(settingsIntent)

            }

            alterDialog.setNegativeButton("Exit") { text, listener ->
                ActivityCompat.finishAffinity(activity as Activity)//closes all the instances of the app and the app closes completely
            }
            alterDialog.create()
            alterDialog.show()

        }
    }

    fun userSuccessfullyRegistered() {
        openDashBoard()
    }

    fun openDashBoard() {

        val intent = Intent(activity as Context, MainActivity::class.java)
        startActivity(intent)
        getActivity()?.finish()

    }

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

        if (etxtConfirmPassword.text.isBlank()) {
            etxtConfirmPassword.setError(contextParam.resources.getString(R.string.field_missing))
        } else {
            errorPassCount++
        }

        if (etxtPassword.text.isBlank()) {
            etxtPassword.setError(contextParam.resources.getString(R.string.field_missing))
        } else {
            errorPassCount++
        }

        if (etxtPassword.text.isNotBlank() && etxtConfirmPassword.text.isNotBlank()) {
            if (etxtPassword.text.toString() == etxtConfirmPassword.text.toString()
            ) {
                errorPassCount++
            } else {
                etxtConfirmPassword.setError(contextParam.resources.getString(R.string.password_dont_match))
            }
        }

        if (errorPassCount == 7)
            return true
        else
            return false
    }

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
                ActivityCompat.finishAffinity(activity as Activity)//closes all the instances of the app and the app closes completely
            }
            alterDialog.setCancelable(false)

            alterDialog.create()
            alterDialog.show()

        }

        super.onResume()
    }
}
