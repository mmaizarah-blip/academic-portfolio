package com.example.smartrent;
import com.google.firebase.auth.FirebaseAuth;


import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import android.widget.TextView;
import android.content.Intent;


import androidx.appcompat.app.AppCompatActivity;

public class RegisterActivity extends AppCompatActivity {

    EditText etName, etEmail, etPassword;
    TextView txtLogin;
    Button btnRegister;

    FirebaseAuth mAuth;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        etName = findViewById(R.id.etName);
        etEmail = findViewById(R.id.etEmail);
        etPassword = findViewById(R.id.etPassword);
        btnRegister = findViewById(R.id.btnRegister);
        mAuth = FirebaseAuth.getInstance();
        txtLogin = findViewById(R.id.txtLogin);

        txtLogin.setOnClickListener(v -> {

            Intent intent =
                    new Intent(
                            RegisterActivity.this,
                            MainActivity.class);

            startActivity(intent);
            finish();

        });

        btnRegister.setOnClickListener(v -> {

            String name = etName.getText().toString();
            String email = etEmail.getText().toString();
            String password = etPassword.getText().toString();

            if(name.isEmpty() || email.isEmpty() || password.isEmpty()) {

                Toast.makeText(this,
                        "Please fill all fields",
                        Toast.LENGTH_SHORT).show();

            } else {

                mAuth.createUserWithEmailAndPassword(email, password)
                        .addOnCompleteListener(task -> {

                            if(task.isSuccessful()) {
                                Toast.makeText(RegisterActivity.this,
                                        "Account Created Successfully",
                                        Toast.LENGTH_SHORT).show();

                                Intent intent = new Intent(
                                        RegisterActivity.this,
                                        OnboardingActivity.class
                                );

                                startActivity(intent);
                                finish();

                            } else {

                                Toast.makeText(RegisterActivity.this,
                                        task.getException().getMessage(),
                                        Toast.LENGTH_LONG).show();

                            }

                        });

            }

        });
    }
}