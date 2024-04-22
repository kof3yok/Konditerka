package com.example.cakeapp.data

import com.example.cakeapp.data.model.LoggedInUser
import java.io.IOException

class LoginDataSource {

    fun login(username: String, password: String): Result<LoggedInUser> {
        try {

            val fakeUser = LoggedInUser(java.util.UUID.randomUUID().toString(), "Jane Doe")
            return Result.Success(fakeUser)
        } catch (e: Throwable) {
            return Result.Error(IOException("Error logging in", e))
        }
    }

    fun logout() {

    }
}
