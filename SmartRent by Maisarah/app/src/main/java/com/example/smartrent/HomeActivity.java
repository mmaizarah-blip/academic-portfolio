package com.example.smartrent;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.auth.FirebaseAuth;


public class HomeActivity extends AppCompatActivity {

    Button btnAddProperty, btnBudget;
    Button btnViewProperties, btnLogout, btnFavourite, btnHelp;
    TextView navHome, navActivities, navProfile;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_home);

        btnAddProperty = findViewById(R.id.btnAddProperty);
        btnViewProperties = findViewById(R.id.btnViewProperties);
        btnBudget = findViewById(R.id.btnBudget);
        btnLogout = findViewById(R.id.btnLogout);
        btnFavourite = findViewById(R.id.btnFavourite);
        btnHelp = findViewById(R.id.btnHelp);
        navHome = findViewById(R.id.navHome);
        navActivities = findViewById(R.id.navActivities);
        navProfile = findViewById(R.id.navProfile);

        btnHelp.setOnClickListener(v -> {
            startActivity(new Intent(HomeActivity.this, OnboardingActivity.class));
        });

        btnAddProperty.setOnClickListener(v -> {
            Intent intent = new Intent(HomeActivity.this, AddPropertyActivity.class);
            startActivity(intent);
        });
        btnBudget.setOnClickListener(v -> {

            Intent intent =
                    new Intent(HomeActivity.this,
                            BudgetAnalysisActivity.class);

            startActivity(intent);

        });
        btnViewProperties.setOnClickListener(v -> {
            Intent intent =
                    new Intent(HomeActivity.this,
                            ViewPropertyActivity.class);

            startActivity(intent);
        });
        btnFavourite.setOnClickListener(v -> {

            Intent intent =
                    new Intent(
                            HomeActivity.this,
                            FavouritePropertiesActivity.class);

            startActivity(intent);

        });
        btnLogout.setOnClickListener(v -> {

            FirebaseAuth.getInstance().signOut();

            Intent intent =
                    new Intent(
                            HomeActivity.this,
                            MainActivity.class);

            startActivity(intent);
            finish();

        });
        navActivities.setOnClickListener(v ->
                startActivity(new Intent(HomeActivity.this, TenantActivitiesActivity.class)));

        navProfile.setOnClickListener(v ->
                startActivity(new Intent(HomeActivity.this, TenantProfileActivity.class)));
    }
}