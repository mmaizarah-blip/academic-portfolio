package com.example.smartrent;

import android.os.Bundle;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.firestore.FirebaseFirestore;

public class LandlordReportActivity extends AppCompatActivity {

    TextView tvMyListings,
            tvMyApproved,
            tvMyPending,
            tvMyRented,
            tvMyRevenue;

    FirebaseFirestore db;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_landlord_report);

        tvMyListings = findViewById(R.id.tvMyListings);
        tvMyApproved = findViewById(R.id.tvMyApproved);
        tvMyPending = findViewById(R.id.tvMyPending);
        tvMyRented = findViewById(R.id.tvMyRented);
        tvMyRevenue = findViewById(R.id.tvMyRevenue);

        db = FirebaseFirestore.getInstance();

        loadReport();
    }

    private void loadReport() {

        String landlordEmail =
                FirebaseAuth.getInstance()
                        .getCurrentUser()
                        .getEmail();

        db.collection("properties")
                .whereEqualTo("landlordEmail", landlordEmail)
                .get()
                .addOnSuccessListener(queryDocumentSnapshots -> {

                    int total = 0;
                    int approved = 0;
                    int pending = 0;
                    int rented = 0;

                    double revenue = 0;

                    for (int i = 0; i < queryDocumentSnapshots.size(); i++) {

                        total++;

                        String approval =
                                queryDocumentSnapshots
                                        .getDocuments()
                                        .get(i)
                                        .getString("approvalStatus");

                        String status =
                                queryDocumentSnapshots
                                        .getDocuments()
                                        .get(i)
                                        .getString("status");

                        if ("Approved".equalsIgnoreCase(approval))
                            approved++;

                        if ("Pending".equalsIgnoreCase(approval))
                            pending++;

                        if ("Rented".equalsIgnoreCase(status))
                            rented++;

                        Object amount =
                                queryDocumentSnapshots
                                        .getDocuments()
                                        .get(i)
                                        .get("paymentAmount");

                        if (amount instanceof Number) {
                            revenue += ((Number) amount).doubleValue();
                        }
                    }

                    tvMyListings.setText("🏠 My Listings: " + total);
                    tvMyApproved.setText("✅ Approved: " + approved);
                    tvMyPending.setText("⏳ Pending: " + pending);
                    tvMyRented.setText("🏡 Rented Properties: " + rented);
                    tvMyRevenue.setText("💵 Revenue: RM " +
                            String.format("%.2f", revenue));
                });
    }
}