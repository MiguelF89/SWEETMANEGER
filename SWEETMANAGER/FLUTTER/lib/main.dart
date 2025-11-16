import 'package:flutter/material.dart';

import 'package:sweetmanager_app/modules/auth/pages/login_page.dart';
import 'package:sweetmanager_app/modules/dashboard/home_page.dart';
import 'package:sweetmanager_app/modules/instituicoes/instituicoes_page.dart';
import 'package:sweetmanager_app/modules/produtos/produtos_page.dart';
import 'package:sweetmanager_app/modules/vendas/vendas_page.dart';

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
        "/instituicoes": (context) => const InstituicoesPage(),
        "/produtos": (context) => const ProdutosPage(),
        "/vendas": (context) => const VendasPage(),
      },
    );
  }
}
  