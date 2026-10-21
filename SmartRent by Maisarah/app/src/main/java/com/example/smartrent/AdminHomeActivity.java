package com.example.smartrent;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.auth.FirebaseAuth;

public class AdminHomeActivity extends AppCompatActivity {

    Button btnValidateListings, btnViewPayments, btnAdminReports,
            btnManageLandlords, btnAdminLogout;

    FirebaseAuth mAuth;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_admin_home);

        btnValidateListings = findViewById(R.id.btnValidateListings);
        btnViewPayments = findViewById(R.id.btnViewPayments);
        btnAdminReports = findViewById(R.id.btnAdminReports);
        btnManageLandlords = findViewById(R.id.btnManageLandlords);
        btnAdminLogout = findViewById(R.id.btnAdminLogout);

        mAuth = FirebaseAuth.getInstance();

        btnValidateListings.setOnClickListener(v -> {
            Intent intent = new Intent(AdminHomeActivity.this, ValidateListingsActivity.class);
            startActivity(intent);
        });

        btnViewPayments.setOnClickListener(v -> {
            Intent intent = new Intent(AdminHomeActivity.this, ViewPaymentsActivity.class);
            startActivity(intent);
        });

        btnAdminReports.setOnClickListener(v -> {
            Intent intent = new Intent(AdminHomeActivity.this, ReportActivity.class);
            startActivity(intent);
        });

        btnManageLandlords.setOnClickListener(v ->{
            Intent intent = new Intent(AdminHomeActivity.this, ManageLandlordsActivity.class);
            startActivity(intent);
        });
        btnAdminLogout.setOnClickListener(v -> {
            mAuth.signOut();
            startActivity(new Intent(AdminHomeActivity.this, MainActivity.class));
            finish();
        });
    }
}