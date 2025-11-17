import 'package:flutter/material.dart';
import '../models/venda.dart';
import '../services/api_service.dart';

class VendasScreen extends StatefulWidget {
  @override
  _VendasScreenState createState() => _VendasScreenState();
}

class _VendasScreenState extends State<VendasScreen> {
  final ApiService _api = ApiService();
  late Future<List<Venda>> futureVendas;

  @override
  void initState() {
    super.initState();
    futureVendas = _api.getVendas();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Gerenciar Vendas")),
      body: FutureBuilder<List<Venda>>(
        future: futureVendas,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator());
          }

          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text("Nenhuma venda encontrada"));
          }

          final vendas = snapshot.data!;
          print(vendas);

          return ListView.builder(
            itemCount: vendas.length,
            itemBuilder: (context, index) {
              final v = vendas[index];
              return ListTile(
                title: Text("Venda #${v.id}"),
                subtitle: Text("Total: R\$ ${v.valorTotal}"),
                onTap: () {},
              );
            },
          );
        },
      ),
    );
  }
}
