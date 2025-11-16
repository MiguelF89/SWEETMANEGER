import 'package:flutter/material.dart';

class VendasPage extends StatelessWidget {
  const VendasPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Vendas")),
      body: const Center(child: Text("Página de Vendas")),
    );
  }
}
