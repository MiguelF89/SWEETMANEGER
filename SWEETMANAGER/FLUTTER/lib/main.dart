import 'package:flutter/material.dart';
import 'modules/auth/pages/login_page.dart';
import 'modules/dashboard/home_page.dart'; // exemplo

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      initialRoute: "/login",
      routes: {
        "/login": (context) => const LoginPage(),
        "/dashboard": (context) => const HomePage(),
      },
    );
  }
}
