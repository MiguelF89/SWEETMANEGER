

import 'package:flutter/material.dart';
import 'home_page.dart';
import 'screens/instituicoes_screen.dart';
import 'screens/produtos_screen.dart';
import 'screens/vendas_screen.dart';
import 'screens/login_screen.dart';
import 'screens/auth_wrapper.dart';
import 'services/auth_service.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Suite Manager',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.blue), 
        useMaterial3: true,
      ),
      initialRoute: '/', 
      routes: {
        '/': (context) => const AuthWrapper(),
        '/login': (context) => const LoginScreen(),
        '/dashboard': (context) => const HomePage(),
        '/instituicoes': (context) =>  InstituicoesScreen(),
        '/produtos': (context) =>  ProdutosScreen(),
        '/vendas': (context) =>  VendasScreen(),
        '/logout': (context) {

          AuthService().logout().then((_) {
            Navigator.of(context).pushNamedAndRemoveUntil('/', (route) => false);
          });
          return const Scaffold(body: Center(child: CircularProgressIndicator()));
        }
      },
    );
  }
}
