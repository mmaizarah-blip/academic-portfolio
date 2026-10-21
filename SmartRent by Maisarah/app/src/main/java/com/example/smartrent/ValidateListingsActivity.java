package com.example.smartrent;

import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.firestore.FirebaseFirestore;

public class ValidateListingsActivity extends AppCompatActivity {

    LinearLayout listingContainer;
    FirebaseFirestore db;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_validate_listings);

        listingContainer = findViewById(R.id.listingContainer);
        db = FirebaseFirestore.getInstance();

        loadPendingListings();
    }

    private void loadPendingListings() {
        db.collection("properties")
                .whereEqualTo("approvalStatus", "Pending")
                .get()
                .addOnSuccessListener(queryDocumentSnapshots -> {

                    if (queryDocumentSnapshots.isEmpty()) {
                        TextView emptyText = new TextView(this);
                        emptyText.setText("No pending listings.");
                        emptyText.setTextSize(18);
                        emptyText.setTextColor(Color.parseColor("#777777"));
                        listingContainer.addView(emptyText);
                        return;
                    }

                    queryDocumentSnapshots.forEach(document -> {

                        String title = document.getString("title");
                        String location = document.getString("location");
                        String type = document.getString("type");

                        if (title == null) title = "Untitled Property";
                        if (location == null) location = "No location";
                        if (type == null) type = "Property";

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
                        titleText.setText("🏠 " + title);
                        titleText.setTextSize(20);
                        titleText.setTypeface(null, Typeface.BOLD);
                        titleText.setTextColor(Color.parseColor("#0F5F3A"));

                        TextView detailText = new TextView(this);
                        detailText.setText(
                                "📍 Location: " + location +
                                        "\nType: " + type +
                                        "\nStatus: Pending Approval"
                        );
                        detailText.setTextSize(15);
                        detailText.setTextColor(Color.parseColor("#333333"));
                        detailText.setPadding(0, 12, 0, 12);

                        Button btnApprove = new Button(this);
                        btnApprove.setText("Approve Listing");
                        btnApprove.setTextColor(Color.WHITE);
                        btnApprove.setBackgroundColor(Color.parseColor("#1E7A5A"));

                        Button btnReject = new Button(this);
                        btnReject.setText("Reject Listing");
                        btnReject.setTextColor(Color.parseColor("#0F5F3A"));
                        btnReject.setBackgroundColor(Color.parseColor("#D9EDE4"));

                        btnApprove.setOnClickListener(v -> {

                            String paymentStatus = document.getString("paymentStatus");

                            if (paymentStatus == null || !paymentStatus.equalsIgnoreCase("Paid")) {
                                Toast.makeText(
                                        this,
                                        "Payment must be paid before approving this listing.",
                                        Toast.LENGTH_LONG
                                ).show();
                                return;
                            }

                            document.getReference()
                                    .update("approvalStatus", "Approved")
                                    .addOnSuccessListener(aVoid -> {
                                        Toast.makeText(this, "Listing approved", Toast.LENGTH_SHORT).show();
                                        listingContainer.removeAllViews();
                                        loadPendingListings();
                                    });
                        });

                        btnReject.setOnClickListener(v -> {
                            document.getReference()
                                    .update("approvalStatus", "Rejected")
                                    .addOnSuccessListener(aVoid -> {
                                        Toast.makeText(this, "Listing rejected", Toast.LENGTH_SHORT).show();
                                        listingContainer.removeAllViews();
                                        loadPendingListings();
                                    });
                        });

                        card.addView(titleText);
                        card.addView(detailText);
                        card.addView(btnApprove);
                        card.addView(btnReject);

                        listingContainer.addView(card);
                    });

                })
                .addOnFailureListener(e ->
                        Toast.makeText(this, "Failed: " + e.getMessage(), Toast.LENGTH_LONG).show());
    }
}