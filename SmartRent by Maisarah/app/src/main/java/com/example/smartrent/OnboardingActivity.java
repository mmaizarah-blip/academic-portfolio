package com.example.smartrent;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

public class OnboardingActivity extends AppCompatActivity {

    TextView tvStep, tvTitle, tvIcon, tvDescription, tvDots;
    Button btnNext, btnSkip;

    int currentStep = 0;

    String[] titles = {
            "Smart Budget Analysis",
            "Browse Approved Properties",
            "Save Favourite Properties",
            "Submit Rental Request",
            "Track Your Rental Journey"
    };

    String[] icons = {
            "💰",
            "🏠",
            "❤️",
            "📝",
            "✅"
    };

    String[] descriptions = {
            "Use your monthly salary and commitments to calculate your suggested property budget before browsing.",
            "Explore approved property listings with photos, prices, location and property details.",
            "Tap the heart icon to save properties you are interested in and view them later.",
            "Submit a rental request directly from the property details page when you find a suitable home.",
            "Your request will be reviewed by the landlord while admins validate listings and payments."
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_onboarding);

        tvStep = findViewById(R.id.tvStep);
        tvTitle = findViewById(R.id.tvTitle);
        tvIcon = findViewById(R.id.tvIcon);
        tvDescription = findViewById(R.id.tvDescription);
        tvDots = findViewById(R.id.tvDots);

        btnNext = findViewById(R.id.btnNext);
        btnSkip = findViewById(R.id.btnSkip);

        showStep();

        btnNext.setOnClickListener(v -> {
            if (currentStep < titles.length - 1) {
                currentStep++;
                showStep();
            } else {
                goHome();
            }
        });

        btnSkip.setOnClickListener(v -> goHome());
    }

    private void showStep() {
        tvStep.setText("Step " + (currentStep + 1) + " of " + titles.length);
        tvTitle.setText(titles[currentStep]);
        tvIcon.setText(icons[currentStep]);
        tvDescription.setText(descriptions[currentStep]);

        StringBuilder dots = new StringBuilder();

        for (int i = 0; i < titles.length; i++) {
            if (i == currentStep) {
                dots.append("● ");
            } else {
                dots.append("○ ");
            }
        }

        tvDots.setText(dots.toString().trim());

        if (currentStep == titles.length - 1) {
            btnNext.setText("Get Started");
        } else {
            btnNext.setText("Next");
        }
    }

    private void goHome() {
        startActivity(new Intent(OnboardingActivity.this, HomeActivity.class));
        finish();
    }
}