package com.example.smartrent;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;
import com.google.firebase.firestore.FirebaseFirestore;
import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.auth.FirebaseAuth;

public class StaffHomeActivity extends AppCompatActivity {

    Button btnAddProperty,
            btnViewProperties,
            btnReport,
            btnRentalRequests,
            btnLogout;
    TextView tvLandlordStats;
    FirebaseFirestore db;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_staff_home);

        btnAddProperty = findViewById(R.id.btnAddProperty);
        btnViewProperties = findViewById(R.id.btnViewProperties);
        btnReport = findViewById(R.id.btnReport);
        btnLogout = findViewById(R.id.btnLogout);
        btnRentalRequests = findViewById(R.id.btnRentalRequests);
        tvLandlordStats = findViewById(R.id.tvLandlordStats);
        db = FirebaseFirestore.getInstance();

        loadLandlordStats();


        btnAddProperty.setOnClickListener(v -> {
            startActivity(new Intent(StaffHomeActivity.this, AddPropertyActivity.class));
        });

        btnViewProperties.setOnClickListener(v -> {
            startActivity(new Intent(StaffHomeActivity.this, ViewPropertyActivity.class));
        });
        btnReport.setOnClickListener(v -> {
            startActivity(new Intent(
                    StaffHomeActivity.this, LandlordReportActivity.class));
        });
        btnRentalRequests.setOnClickListener(v -> {
            Intent intent = new Intent(
                    StaffHomeActivity.this,
                    ViewRentRequestsActivity.class);

            startActivity(intent);
        });

        btnLogout.setOnClickListener(v -> {

            FirebaseAuth.getInstance().signOut();

            Intent intent =
                    new Intent(
                            StaffHomeActivity.this,
                            MainActivity.class);

            startActivity(intent);
            finish();

        });
    }
    private void loadLandlordStats() {

        String landlordEmail = "";

        if (FirebaseAuth.getInstance().getCurrentUser() != null) {
            landlordEmail = FirebaseAuth.getInstance().getCurrentUser().getEmail();
        }

        String finalLandlordEmail = landlordEmail;

        db.collection("properties")
                .whereEqualTo("landlordEmail", finalLandlordEmail)
                .get()
                .addOnSuccessListener(propertySnapshots -> {

                    int totalListings = propertySnapshots.size();

                    db.collection("rent_requests")
                            .get()
                            .addOnSuccessListener(requestSnapshots -> {

                                int totalRequests = requestSnapshots.size();

                                tvLandlordStats.setText(
                                        "🏠 Listings: " + totalListings +
                                                "\n📩 Requests: " + totalRequests
                                );
                            });
                });
    }
}