package com.example.smartrent;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.auth.FirebaseAuth;

public class TenantProfileActivity extends AppCompatActivity {

    TextView tvProfileEmail, navHome, navActivities, navProfile;
    Button btnHelp, btnLogout;
    Button btnMyRequests, btnProfileFavourite;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_tenant_profile);

        tvProfileEmail = findViewById(R.id.tvProfileEmail);
        btnHelp = findViewById(R.id.btnHelp);
        btnLogout = findViewById(R.id.btnLogout);

        navHome = findViewById(R.id.navHome);
        navActivities = findViewById(R.id.navActivities);
        navProfile = findViewById(R.id.navProfile);
        btnMyRequests = findViewById(R.id.btnMyRequests);
        btnProfileFavourite = findViewById(R.id.btnProfileFavourite);

        btnMyRequests.setOnClickListener(v ->
                startActivity(new Intent(
                        TenantProfileActivity.this,
                        MyRentalRequestsActivity.class)));

        btnProfileFavourite.setOnClickListener(v ->
                startActivity(new Intent(
                        TenantProfileActivity.this,
                        FavouritePropertiesActivity.class)));

        if (FirebaseAuth.getInstance().getCurrentUser() != null) {
            tvProfileEmail.setText("📧 " +
                    FirebaseAuth.getInstance().getCurrentUser().getEmail());
        }

        btnHelp.setOnClickListener(v ->
                startActivity(new Intent(
                        TenantProfileActivity.this,
                        OnboardingActivity.class)));

        btnLogout.setOnClickListener(v -> {
            FirebaseAuth.getInstance().signOut();
            startActivity(new Intent(
                    TenantProfileActivity.this,
                    MainActivity.class));
            finish();
        });

        navHome.setOnClickListener(v ->
                startActivity(new Intent(
                        TenantProfileActivity.this,
                        HomeActivity.class)));

        navActivities.setOnClickListener(v ->
                startActivity(new Intent(
                        TenantProfileActivity.this,
                        TenantActivitiesActivity.class)));
    }
}