package com.example.smartrent;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

public class TenantActivitiesActivity extends AppCompatActivity {

    Button btnFavourites, btnRequests, btnBudget;
    TextView navHome, navActivities, navProfile;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_tenant_activities);

        btnFavourites = findViewById(R.id.btnFavourites);
        btnRequests = findViewById(R.id.btnRequests);
        btnBudget = findViewById(R.id.btnBudget);

        navHome = findViewById(R.id.navHome);
        navActivities = findViewById(R.id.navActivities);
        navProfile = findViewById(R.id.navProfile);

        btnFavourites.setOnClickListener(v ->
                startActivity(new Intent(
                        TenantActivitiesActivity.this,
                        FavouritePropertiesActivity.class)));

        btnBudget.setOnClickListener(v ->
                startActivity(new Intent(
                        TenantActivitiesActivity.this,
                        BudgetAnalysisActivity.class)));

        btnRequests.setOnClickListener(v ->
                startActivity(new Intent(
                        TenantActivitiesActivity.this,
                        MyRentalRequestsActivity.class)));

        navHome.setOnClickListener(v ->
                startActivity(new Intent(
                        TenantActivitiesActivity.this,
                        HomeActivity.class)));

        navProfile.setOnClickListener(v ->
                startActivity(new Intent(
                        TenantActivitiesActivity.this,
                        TenantProfileActivity.class)));
    }
}