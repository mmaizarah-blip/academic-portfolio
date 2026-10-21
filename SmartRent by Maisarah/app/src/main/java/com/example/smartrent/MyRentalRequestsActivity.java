package com.example.smartrent;

import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.firestore.FirebaseFirestore;

public class MyRentalRequestsActivity extends AppCompatActivity {

    LinearLayout requestContainer;
    TextView tvRequestSummary;
    FirebaseFirestore db;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_rental_requests);

        requestContainer = findViewById(R.id.requestContainer);
        tvRequestSummary = findViewById(R.id.tvRequestSummary);
        db = FirebaseFirestore.getInstance();

        loadMyRequests();
    }

    private void loadMyRequests() {
        String userEmail = "";

        if (FirebaseAuth.getInstance().getCurrentUser() != null) {
            userEmail = FirebaseAuth.getInstance().getCurrentUser().getEmail();
        }

        db.collection("rent_requests")
                .whereEqualTo("userEmail", userEmail)
                .get()
                .addOnSuccessListener(queryDocumentSnapshots -> {

                    int pending = 0;
                    int approved = 0;
                    int rejected = 0;
                    int completed = 0;

                    for (int i = 0; i < queryDocumentSnapshots.size(); i++) {
                        String status = queryDocumentSnapshots.getDocuments().get(i).getString("status");

                        if (status == null) status = "Pending";

                        if (status.equalsIgnoreCase("Approved")) {
                            approved++;
                        } else if (status.equalsIgnoreCase("Rejected")) {
                            rejected++;
                        } else if (status.equalsIgnoreCase("Completed")) {
                            completed++;
                        } else {
                            pending++;
                        }
                    }

                    tvRequestSummary.setText(
                            "⏳ Pending: " + pending +
                                    "\n✅ Approved: " + approved +
                                    "\n❌ Rejected: " + rejected +
                                    "\n🏁 Completed: " + completed
                    );

                    if (queryDocumentSnapshots.isEmpty()) {
                        TextView emptyText = new TextView(this);
                        emptyText.setText("No rental requests found.");
                        emptyText.setTextSize(18);
                        emptyText.setTextColor(Color.parseColor("#777777"));
                        requestContainer.addView(emptyText);
                        return;
                    }

                    for (int i = 0; i < queryDocumentSnapshots.size(); i++) {

                        var document = queryDocumentSnapshots.getDocuments().get(i);

                        String propertyTitle = document.getString("propertyTitle");
                        String status = document.getString("status");
                        String name = document.getString("name");
                        String phone = document.getString("phone");
                        String message = document.getString("message");
                        String moveInDate = document.getString("moveInDate");

                        if (propertyTitle == null) propertyTitle = "Untitled Property";
                        if (status == null) status = "Pending";
                        if (name == null) name = "-";
                        if (phone == null) phone = "-";
                        if (moveInDate == null) moveInDate = "-";
                        if (message == null || message.isEmpty()) message = "No additional message.";

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

                        TextView titleText = new TextView(this);
                        titleText.setText("🏠 " + propertyTitle);
                        titleText.setTextSize(20);
                        titleText.setTypeface(null, Typeface.BOLD);
                        titleText.setTextColor(Color.parseColor("#0F5F3A"));

                        TextView statusText = new TextView(this);
                        statusText.setText("Status: " + status);
                        statusText.setTextSize(16);
                        statusText.setTypeface(null, Typeface.BOLD);
                        statusText.setPadding(0, 10, 0, 8);

                        if (status.equalsIgnoreCase("Approved")) {
                            statusText.setTextColor(Color.parseColor("#1E7A5A"));
                        } else if (status.equalsIgnoreCase("Rejected")) {
                            statusText.setTextColor(Color.parseColor("#D32F2F"));
                        } else if (status.equalsIgnoreCase("Completed")) {
                            statusText.setTextColor(Color.parseColor("#0F5F3A"));
                        } else {
                            statusText.setTextColor(Color.parseColor("#F9A825"));
                        }

                        TextView detailText = new TextView(this);
                        detailText.setText(
                                "Applicant: " + name +
                                        "\nPhone: " + phone +
                                        "\nMove In Date: " + moveInDate +
                                        "\nMessage: " + message
                        );
                        detailText.setTextSize(15);
                        detailText.setTextColor(Color.parseColor("#333333"));
                        detailText.setPadding(0, 8, 0, 0);

                        card.addView(titleText);
                        card.addView(statusText);
                        card.addView(detailText);

                        requestContainer.addView(card);
                    }

                })
                .addOnFailureListener(e ->
                        Toast.makeText(this,
                                "Failed: " + e.getMessage(),
                                Toast.LENGTH_LONG).show());
    }
}