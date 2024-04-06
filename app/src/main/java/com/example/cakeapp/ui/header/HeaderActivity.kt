package com.example.cakeapp.ui.header

import android.content.Intent
import android.os.Bundle
import android.os.PersistableBundle
import android.view.View
import android.widget.ImageButton
import androidx.appcompat.app.AppCompatActivity
import com.example.cakeapp.R
import com.example.cakeapp.databinding.HeaderBinding
import com.example.cakeapp.ui.login.LoginActivity

class HeaderActivity : AppCompatActivity() {

    private lateinit var binding: HeaderBinding
    override fun onCreate(savedInstanceState: Bundle?, persistentState: PersistableBundle?) {
        super.onCreate(savedInstanceState, persistentState);
        setContentView(R.layout.header);
        val ibtnUser = binding.ibtnUser;
        ibtnUser.setOnClickListener(View.OnClickListener {
            onOpenProfile()
        })
    }

    private fun onOpenProfile() {
        val intent = Intent(this, LoginActivity::class.java);
        startActivity(intent);
        finish();
    }
}