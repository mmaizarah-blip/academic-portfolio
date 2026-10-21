package com.example.smartrent;

import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.firestore.FirebaseFirestore;

import java.util.HashMap;
import java.util.Map;

public class ManageLandlordsActivity extends AppCompatActivity {

    LinearLayout landlordContainer;
    FirebaseFirestore db;

    Map<String, LandlordStats> landlordMap = new HashMap<>();

    static class LandlordStats {
        int totalListings = 0;
        int approvedListings = 0;
        int pendingListings = 0;
        int rejectedListings = 0;
        double revenue = 0;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_manage_landlords);

        landlordContainer = findViewById(R.id.landlordContainer);
        db = FirebaseFirestore.getInstance();

        loadLandlords();
    }

    private void loadLandlords() {
        db.collection("properties")
                .get()
                .addOnSuccessListener(queryDocumentSnapshots -> {

                    landlordMap.clear();

                    for (int i = 0; i < queryDocumentSnapshots.size(); i++) {

                        var document = queryDocumentSnapshots.getDocuments().get(i);

                        String landlordEmail = document.getString("landlordEmail");
                        String approvalStatus = document.getString("approvalStatus");
                        String paymentStatus = document.getString("paymentStatus");

                        if (landlordEmail == null || landlordEmail.isEmpty()) {
                            landlordEmail = "Unknown Landlord";
                        }

                        LandlordStats stats = landlordMap.get(landlordEmail);

                        if (stats == null) {
                            stats = new LandlordStats();
                            landlordMap.put(landlordEmail, stats);
                        }

                        stats.totalListings++;

                        if (approvalStatus != null) {
                            if (approvalStatus.equalsIgnoreCase("Approved")) {
                                stats.approvedListings++;
                            } else if (approvalStatus.equalsIgnoreCase("Pending")) {
                                stats.pendingListings++;
                            } else if (approvalStatus.equalsIgnoreCase("Rejected")) {
                                stats.rejectedListings++;
                            }
                        }

                        Object paymentAmountObj = document.get("paymentAmount");
                        double paymentAmount = 0;

                        if (paymentAmountObj instanceof Number) {
                            paymentAmount = ((Number) paymentAmountObj).doubleValue();
                        }

                        if (paymentStatus != null &&
                                paymentStatus.equalsIgnoreCase("Paid")) {
                            stats.revenue += paymentAmount;
                        }
                    }

                    showLandlordCards();

                })
                .addOnFailureListener(e ->
                        Toast.makeText(this, "Failed: " + e.getMessage(), Toast.LENGTH_LONG).show());
    }

    private void showLandlordCards() {
        landlordContainer.removeAllViews();

        TextView title = new TextView(this);
        title.setText("Manage Landlords");
        title.setTextSize(30);
        title.setTypeface(null, Typeface.BOLD);
        title.setTextColor(Color.parseColor("#0F5F3A"));

        TextView subtitle = new TextView(this);
        subtitle.setText("View landlord listing performance and revenue.");
        subtitle.setTextSize(15);
        subtitle.setTextColor(Color.parseColor("#555555"));
        subtitle.setPadding(0, 6, 0, 20);

        landlordContainer.addView(title);
        landlordContainer.addView(subtitle);

        if (landlordMap.isEmpty()) {
            TextView emptyText = new TextView(this);
            emptyText.setText("No landlord listings found.");
            emptyText.setTextSize(18);
            emptyText.setTextColor(Color.parseColor("#777777"));
            landlordContainer.addView(emptyText);
            return;
        }

        for (String email : landlordMap.keySet()) {

            LandlordStats stats = landlordMap.get(email);

            LinearLayout card = new LinearLayout(this);
            card.setOrientation(LinearLayout.VERTICAL);
            card.setPadding(32, 28, 32, 28);
            card.setBackgroundColor(Color.WHITE);

            LinearLayout.LayoutParams params =
                    new LinearLayout.LayoutParams(
                            ViewGroup.LayoutParams.MATCH_PARENT,
                            ViewGroup.LayoutParams.WRAP_CONTENT
                    );

            params.setMargins(0, 0, 0, 20);
            card.setLayoutParams(params);
            card.setElevation(8f);

            TextView emailText = new TextView(this);
            emailText.setText("🏠 " + email);
            emailText.setTextSize(18);
            emailText.setTypeface(null, Typeface.BOLD);
            emailText.setTextColor(Color.parseColor("#0F5F3A"));

            TextView detailText = new TextView(this);
            detailText.setText(
                    "Total Listings: " + stats.totalListings +
                            "\nApproved Listings: " + stats.approvedListings +
                            "\nPending Listings: " + stats.pendingListings +
                            "\nRejected Listings: " + stats.rejectedListings +
                            "\nRevenue Generated: RM " + String.format("%.2f", stats.revenue)
            );
            detailText.setTextSize(15);
            detailText.setTextColor(Color.parseColor("#333333"));
            detailText.setPadding(0, 12, 0, 0);

            card.addView(emailText);
            card.addView(detailText);

            landlordContainer.addView(card);
        }
    }
}