package com.example.cakeapp.ui.login

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.opengl.Visibility
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
import androidx.fragment.app.FragmentManager
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.R
import com.example.cakeapp.data.model.LoginResult
import com.example.cakeapp.history_cart
import com.example.cakeapp.utils.ConnectionManager
import com.google.gson.Gson
import org.json.JSONException
import org.json.JSONObject

class ForgotPasswordFragment(val contextParam: Context,
                             val fm: FragmentManager
) : Fragment() {

    private lateinit var etxtUsername: EditText
    private lateinit var etxtMobileNumber: EditText
    private lateinit var etxtCode: EditText
    private lateinit var etxtConfirmPassword: EditText
    private lateinit var etxtConfirmPassword2: EditText
    private lateinit var btnChangePwd: Button
    private lateinit var btnSendCode: Button
    private lateinit var register_fragment_Progressdialog: RelativeLayout
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        // Inflate the layout for this fragment
        val view = inflater.inflate(R.layout.fragment_forgot_password, container, false)

        etxtUsername = view.findViewById(R.id.etxtUserName)
        etxtCode = view.findViewById(R.id.etxtCode)
        etxtConfirmPassword = view.findViewById(R.id.etxtPassword)
        etxtConfirmPassword2 = view.findViewById(R.id.etxtPassword2)
        btnSendCode = view.findViewById(R.id.btnSendCode)
        btnChangePwd = view.findViewById(R.id.btnChangePwd)
        register_fragment_Progressdialog = view.findViewById(R.id.Progressdialog)

        btnSendCode.setOnClickListener(View.OnClickListener {
            sendCode()
        })
        btnChangePwd.setOnClickListener(View.OnClickListener {
            changePwd()
        })
        return view
    }

    fun sendCode() {
        if (ConnectionManager().checkConnectivity(activity as Context)) {
            if (!etxtUsername.text.isBlank()) {

                register_fragment_Progressdialog.visibility = View.VISIBLE
                try {
                    val registerUser = JSONObject()
                    registerUser.put("username", etxtUsername.text)

                    val queue = Volley.newRequestQueue(activity as Context)
                    val url = resources.getString(R.string.url) + getString(R.string.sendcode)

                    val jsonObjectRequest = object : JsonObjectRequest(
                        Request.Method.POST,
                        url,
                        registerUser,
                        Response.Listener {
                            val gson = Gson()
                            val result = gson.fromJson(it.toString(), LoginResult::class.java)

                            if (result.token != "" && result.message === null && result.status == 200) {

                                Toast.makeText(
                                    contextParam,
                                    "Код отправлен в письме",
                                    Toast.LENGTH_SHORT
                                ).show()
                                etxtUsername.isEnabled = false
                                btnSendCode.isEnabled = false
                                etxtCode.visibility = View.VISIBLE
                                etxtConfirmPassword.visibility = View.VISIBLE
                                etxtConfirmPassword2.visibility = View.VISIBLE
                                btnChangePwd.visibility = View.VISIBLE

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

    fun changePwd() {
        if (ConnectionManager().checkConnectivity(activity as Context)) {
            if (
                !etxtUsername.text.isBlank() &&
                !etxtCode.text.isBlank() &&
                !etxtConfirmPassword.text.isBlank() &&
                !etxtConfirmPassword2.text.isBlank() &&
                etxtConfirmPassword.text.toString() == etxtConfirmPassword2.text.toString()
            ) {

                register_fragment_Progressdialog.visibility = View.VISIBLE
                try {
                    val registerUser = JSONObject()
                    registerUser.put("username", etxtUsername.text)
                    registerUser.put("code", etxtCode.text)
                    registerUser.put("password", etxtConfirmPassword.text)

                    val queue = Volley.newRequestQueue(activity as Context)
                    val url = resources.getString(R.string.url) + getString(R.string.changepwd)

                    val jsonObjectRequest = object : JsonObjectRequest(
                        Request.Method.POST,
                        url,
                        registerUser,
                        Response.Listener {
                            val gson = Gson()
                            val result = gson.fromJson(it.toString(), LoginResult::class.java)

                            if (result.token != "" && result.message === null && result.status == 200) {

                                Toast.makeText(
                                    contextParam,
                                    "Пароль поменяли!",
                                    Toast.LENGTH_SHORT
                                ).show()
                                fm.beginTransaction().replace(
                                    R.id.flFragment,
                                    LoginActivity(contextParam,fm)
                                ).commit()

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

}