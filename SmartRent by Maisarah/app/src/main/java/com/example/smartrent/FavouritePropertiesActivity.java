package com.example.smartrent;

import android.content.Intent;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.bumptech.glide.Glide;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.firestore.FirebaseFirestore;

import java.text.NumberFormat;
import java.util.Locale;

public class FavouritePropertiesActivity extends AppCompatActivity {

    LinearLayout favouriteContainer;
    FirebaseFirestore db;
    String userEmail = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_favourite_properties);

        favouriteContainer = findViewById(R.id.favouriteContainer);
        db = FirebaseFirestore.getInstance();

        if (FirebaseAuth.getInstance().getCurrentUser() != null) {
            userEmail = FirebaseAuth.getInstance().getCurrentUser().getEmail();
        }

        loadFavourites();
    }

    private void loadFavourites() {
        db.collection("favourites")
                .whereEqualTo("userEmail", userEmail)
                .get()
                .addOnSuccessListener(queryDocumentSnapshots -> {

                    if (queryDocumentSnapshots.isEmpty()) {
                        TextView emptyText = new TextView(this);
                        emptyText.setText("No favourite properties yet.");
                        emptyText.setTextSize(18);
                        emptyText.setTextColor(Color.parseColor("#777777"));
                        favouriteContainer.addView(emptyText);
                        return;
                    }

                    queryDocumentSnapshots.forEach(document -> {

                        String title = document.getString("title");
                        String location = document.getString("location");
                        String imageUrl = document.getString("imageUrl");
                        String propertyId = document.getString("propertyId");

                        Object priceObj = document.get("price");
                        double price = 0;

                        if (priceObj instanceof Number) {
                            price = ((Number) priceObj).doubleValue();
                        }

                        if (title == null) title = "Untitled Property";
                        if (location == null) location = "No location";

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

                        ImageView img = new ImageView(this);
                        img.setLayoutParams(new LinearLayout.LayoutParams(
                                ViewGroup.LayoutParams.MATCH_PARENT,
                                360
                        ));
                        img.setScaleType(ImageView.ScaleType.CENTER_CROP);
                        img.setBackgroundColor(Color.parseColor("#D9EDE4"));

                        if (imageUrl != null && !imageUrl.isEmpty()) {
                            Glide.with(this).load(imageUrl).into(img);
                        }

                        TextView titleText = new TextView(this);
                        titleText.setText("🏠 " + title);
                        titleText.setTextSize(20);
                        titleText.setTypeface(null, Typeface.BOLD);
                        titleText.setTextColor(Color.parseColor("#0F5F3A"));
                        titleText.setPadding(0, 14, 0, 0);

                        TextView detailText = new TextView(this);
                        NumberFormat formatter = NumberFormat.getNumberInstance(Locale.US);
                        detailText.setText("📍 " + location + "\nRM " + formatter.format(price));
                        detailText.setTextSize(15);
                        detailText.setTextColor(Color.parseColor("#333333"));
                        detailText.setPadding(0, 8, 0, 12);

                        Button btnView = new Button(this);
                        btnView.setText("View Details");
                        btnView.setTextColor(Color.WHITE);
                        btnView.setBackgroundColor(Color.parseColor("#1E7A5A"));

                        String finalPropertyId = propertyId;

                        btnView.setOnClickListener(v -> {
                            Intent intent = new Intent(
                                    FavouritePropertiesActivity.this,
                                    PropertyDetailActivity.class
                            );
                            intent.putExtra("propertyId", finalPropertyId);
                            startActivity(intent);
                        });

                        card.addView(img);
                        card.addView(titleText);
                        card.addView(detailText);
                        card.addView(btnView);

                        favouriteContainer.addView(card);
                    });

                })
                .addOnFailureListener(e ->
                        Toast.makeText(this, "Failed: " + e.getMessage(), Toast.LENGTH_LONG).show());
    }
}