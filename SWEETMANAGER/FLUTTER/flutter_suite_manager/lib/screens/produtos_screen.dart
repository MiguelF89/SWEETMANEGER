import 'package:flutter/material.dart';
import '../models/produto.dart';
import '../services/api_service.dart';

class ProdutosScreen extends StatefulWidget {
  @override
  _ProdutosScreenState createState() => _ProdutosScreenState();
}

class _ProdutosScreenState extends State<ProdutosScreen> {
  final ApiService _api = ApiService();
  late Future<List<Produto>> futureProdutos;

  @override
  void initState() {
    super.initState();
    futureProdutos = _api.getProdutos();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Gerenciar Produtos")),
      body: FutureBuilder<List<Produto>>(
        future: futureProdutos,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator());
          }

          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text("Nenhum produto encontrado"));
          }

          final produtos = snapshot.data!;

          return ListView.builder(
            itemCount: produtos.length,
            itemBuilder: (context, index) {
              final p = produtos[index];
              return ListTile(
                title: Text(p.nome),
                subtitle: Text("R\$ ${p.preco.toStringAsFixed(2)}"),
                onTap: () {},
              );
            },
          );
        },
      ),
    );
  }
}
