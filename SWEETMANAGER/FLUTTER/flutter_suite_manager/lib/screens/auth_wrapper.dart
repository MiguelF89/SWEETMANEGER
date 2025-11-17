// lib/screens/auth_wrapper.dart

import 'package:flutter/material.dart';
import '../services/auth_service.dart'; 
import '../home_page.dart';
import 'login_screen.dart';

class AuthWrapper extends StatelessWidget {
  const AuthWrapper({super.key});

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<bool>(
      future: AuthService().isLoggedIn(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {

          return const Scaffold(
            body: Center(child: CircularProgressIndicator()),
          );
        }
        
        final isLoggedIn = snapshot.data ?? false;

        if (isLoggedIn) {

          return const HomePage();
        } else {

          return const LoginScreen();
        }
      },
    );
  }
}
