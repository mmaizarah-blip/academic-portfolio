package com.example.smartrent;

import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.view.animation.AlphaAnimation;
import android.widget.ImageView;

import androidx.appcompat.app.AppCompatActivity;

public class SplashActivity extends AppCompatActivity {

    ImageView imgSplash;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);

        imgSplash = findViewById(R.id.imgSplash);

        imgSplash.setImageResource(R.drawable.welcome_screen);

        AlphaAnimation fadeIn =
                new AlphaAnimation(0f, 1f);

        fadeIn.setDuration(1000);

        imgSplash.startAnimation(fadeIn);

        new Handler().postDelayed(() -> {

            AlphaAnimation fadeOut =
                    new AlphaAnimation(1f, 0f);

            fadeOut.setDuration(1000);

            imgSplash.startAnimation(fadeOut);

            new Handler().postDelayed(() -> {

                Intent intent =
                        new Intent(
                                SplashActivity.this,
                                MainActivity.class);

                startActivity(intent);
                finish();

            }, 1000);

        }, 3000);
    }
}