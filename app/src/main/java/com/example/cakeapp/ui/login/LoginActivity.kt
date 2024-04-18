// Код, отвечающий за выполнение необходимых функций при входе в приложение
package com.example.cakeapp.ui.login

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.provider.Settings
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.RelativeLayout
import android.widget.TextView
import android.widget.Toast
import androidx.core.app.ActivityCompat
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentManager
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.cakeapp.ItemFragment
import com.example.cakeapp.MainActivity
import com.example.cakeapp.ProfileFragment
import org.json.JSONException
import org.json.JSONObject

import com.example.cakeapp.R
import com.example.cakeapp.data.model.LoginResult
import com.example.cakeapp.data.model.User
import com.example.cakeapp.utils.ConnectionManager
import com.google.gson.Gson

class LoginActivity(val contextParam: Context,val fm:FragmentManager) : Fragment() {
    //    private lateinit var binding:
    lateinit var textViewSignUp: TextView
    lateinit var textViewForgotPassword: TextView
    private lateinit var phone: EditText
    private lateinit var password: EditText
    private lateinit var btnLogin: Button
    lateinit var login_fragment_Progressdialog: RelativeLayout
    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.activity_login, container, false)
        btnLogin = view.findViewById<Button>(R.id.btnLogin)
        phone = view.findViewById<EditText>(R.id.etxtPhone)
        password = view.findViewById<EditText>(R.id.etxtPassword)
        login_fragment_Progressdialog = view.findViewById(R.id.login_fragment_Progressdialog)
        textViewForgotPassword = view.findViewById(R.id.txtForgot)
        textViewSignUp = view.findViewById(R.id.txtRegister)

        login_fragment_Progressdialog.visibility = View.INVISIBLE
        btnLogin.setOnClickListener {
            onLogin()
        }
        textViewForgotPassword.setOnClickListener(View.OnClickListener {
            openForgotPasswordInputFragment()
        })


        textViewSignUp.setOnClickListener(View.OnClickListener {
            openRegisterFragment()
        })
        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )

        val id = sp.getString("user_id", "").toString()
        if (id != "")
            openProfileFragment()
        return view;
    }

    fun openForgotPasswordInputFragment() {
        val transaction = fragmentManager?.beginTransaction()
        transaction?.replace(
            R.id.flFragment,
            ForgotPasswordFragment(contextParam,fm)
        )//replace the old layout with the new frag  layout

        transaction?.commit()//apply changes

    }

    fun openRegisterFragment() {

        val transaction = fragmentManager?.beginTransaction()

        transaction?.replace(
            R.id.flFragment,
            RegisterFragment(contextParam)
        )//replace the old layout with the new frag  layout

        transaction?.commit()//apply changes


    }

    fun openProfileFragment() {

        val transaction = fragmentManager?.beginTransaction()

        transaction?.replace(
            R.id.flFragment,
            ProfileFragment(contextParam,fm)
        )//replace the old layout with the new frag  layout

        transaction?.commit()//apply changes


    }

    fun openMainActivityFragment() {

        val transaction = fragmentManager?.beginTransaction()

        transaction?.replace(
            R.id.flFragment,
            ProfileFragment(contextParam,fm)
        )//replace the old layout with the new frag  layout

        transaction?.commit()//apply changes


    }

    private fun onLogin() {
        val phone = phone.text.trim()
        var password = password.text.trim()
        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        );

        if (ConnectionManager().checkConnectivity(activity as Context)) {
            try {

                login_fragment_Progressdialog.visibility = View.VISIBLE
                val loginUser = JSONObject()

                loginUser.put("username", phone)
                loginUser.put("password", password)

                val queue = Volley.newRequestQueue(activity as Context)

                val url = resources.getString(R.string.url)
                    .toString() + resources.getString(R.string.Login)

                val jsonObjectRequest = object : JsonObjectRequest(
                    Request.Method.POST,
                    url,
                    loginUser,
                    Response.Listener {

                        val gson = Gson()
                        val result = gson.fromJson(it.toString(), LoginResult::class.java)

                        if (result.token != "" && result.message == null && result.status == 200) {

                            sp.edit().putString("user_id", result.token).commit()
                            getUser()
                            openMainActivityFragment()
                        } else if (result.token == null && result.message != null && result.status == 200) {

                            Toast.makeText(
                                contextParam,
                                result.message,
                                Toast.LENGTH_SHORT
                            ).show()
                        }

                    },
                    Response.ErrorListener {
                        println(it)
                        btnLogin.visibility = View.VISIBLE

                        Toast.makeText(
                            contextParam,
                            "Some Error occurred!!!",
                            Toast.LENGTH_SHORT
                        ).show()
                    }
                ) {
                    override fun getHeaders(): MutableMap<String, String> {
                        val headers = HashMap<String, String>()

                        headers["Content-type"] = "application/json"
//                        headers["token"] = getString(R.string.token)

                        return headers
                    }
                }

                queue.add(jsonObjectRequest)

            } catch (e: JSONException) {
                btnLogin.visibility = View.VISIBLE

                Toast.makeText(
                    contextParam,
                    "Some unexpected error occured!!!",
                    Toast.LENGTH_SHORT
                ).show()
            } finally {
                login_fragment_Progressdialog.visibility = View.INVISIBLE
            }
        } else {
            btnLogin.visibility = View.VISIBLE

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

    fun getUser() {

        val sp = contextParam.getSharedPreferences(
            R.string.shared_preferences.toString(),
            Context.MODE_PRIVATE
        )
        val id = sp.getString("user_id", "").toString()
        if (ConnectionManager().checkConnectivity(activity as Context)) {
            if (true) {

                login_fragment_Progressdialog.visibility = View.VISIBLE
                try {
                    val registerUser = JSONObject()
                    registerUser.put("token", id)
//                    registerUser.put("username", etxtUsername.text)
//                    registerUser.put("phone", etxtMobileNumber.text)
//                    registerUser.put("password", etxtPassword.text)
//                    registerUser.put("address", etxtDeliveryAddress.text)
//                    registerUser.put("email", etxtEmail.text)

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


                                sp.edit()
                                    .putString("user_address", userList[0].Address)
                                    .commit()
                                Toast.makeText(
                                    contextParam,
                                    "SUCCESS",
                                    Toast.LENGTH_SHORT
                                ).show()

                                login_fragment_Progressdialog.visibility = View.INVISIBLE

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
                            login_fragment_Progressdialog.visibility = View.INVISIBLE
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
