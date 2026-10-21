package com.example.smartrent;

import com.google.firebase.auth.FirebaseAuth;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import android.content.Intent;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

public class MainActivity extends AppCompatActivity {

    EditText etEmail, etPassword;
    Button btnLogin;
    TextView txtRegister;

    FirebaseAuth mAuth;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        etEmail = findViewById(R.id.etEmail);
        etPassword = findViewById(R.id.etPassword);
        btnLogin = findViewById(R.id.btnLogin);
        txtRegister = findViewById(R.id.txtRegister);
        mAuth = FirebaseAuth.getInstance();

        btnLogin.setOnClickListener(v -> {

            String email = etEmail.getText().toString();
            String password = etPassword.getText().toString();

            if(email.isEmpty() || password.isEmpty()){
                Toast.makeText(this,
                        "Please fill all fields",
                        Toast.LENGTH_SHORT).show();
            } else {
                mAuth.signInWithEmailAndPassword(email, password)
                        .addOnCompleteListener(task -> {

                            if(task.isSuccessful()) {

                                Toast.makeText(MainActivity.this,
                                        "Login Successful",
                                        Toast.LENGTH_SHORT).show();

                                String userEmail = mAuth.getCurrentUser().getEmail();

                                Intent intent;

                                if(userEmail != null &&
                                        userEmail.endsWith("@admin.smartrent.com.my")) {

                                    intent = new Intent(
                                            MainActivity.this,
                                            AdminHomeActivity.class);

                                }
                                else if(userEmail != null &&
                                        userEmail.endsWith("@landlord.smartrent.com.my")) {

                                    intent = new Intent(
                                            MainActivity.this,
                                            StaffHomeActivity.class); // later rename to LandlordHomeActivity

                                }
                                else {

                                    intent = new Intent(
                                            MainActivity.this,
                                            HomeActivity.class); // Tenant

                                }

                                startActivity(intent);
                                finish();

                            } else {

                                Toast.makeText(MainActivity.this,
                                        task.getException().getMessage(),
                                        Toast.LENGTH_LONG).show();

                            }

                        });
            }

        });
        txtRegister.setOnClickListener(v -> {

            Intent intent = new Intent(
                    MainActivity.this,
                    RegisterActivity.class
            );

            startActivity(intent);

        });
    }
}