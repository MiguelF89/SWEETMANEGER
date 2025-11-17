

import 'package:flutter/material.dart';

class HomePage extends StatelessWidget {
  const HomePage({super.key});


  Widget _buildDashboardCard({
    required BuildContext context,
    required String title,
    required IconData icon,
    required String routeName,
    required Color color,
  }) {
    return Card(
      elevation: 5,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(15.0),
      ),
      child: InkWell(
        onTap: () => Navigator.pushNamed(context, routeName),
        borderRadius: BorderRadius.circular(15.0),
        child: Container(
          padding: const EdgeInsets.all(16.0),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(15.0),
            color: color.withOpacity(0.1), 
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                icon,
                size: 48,
                color: color,
              ),
              const SizedBox(height: 10),
              Text(
                title,
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: color,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(
          "Sweet Manager Dashboard",
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white),
        ),
        centerTitle: true,
        backgroundColor: Theme.of(context).colorScheme.primary, 
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: GridView.count(
          crossAxisCount: 2, 
          crossAxisSpacing: 16.0,
          mainAxisSpacing: 16.0,
          children: [
          
            _buildDashboardCard(
              context: context,
              title: "Gerenciar Instituições",
              icon: Icons.business,
              routeName: "/instituicoes",
              color: Colors.blue.shade700,
            ),

            // Card para Produtos
            _buildDashboardCard(
              context: context,
              title: "Gerenciar Produtos",
              icon: Icons.inventory_2,
              routeName: "/produtos",
              color: Colors.green.shade700,
            ),

            // Card para Vendas
            _buildDashboardCard(
              context: context,
              title: "Gerenciar Vendas",
              icon: Icons.shopping_cart,
              routeName: "/vendas",
              color: Colors.orange.shade700,
            ),
            
            // Card de Logout
            _buildDashboardCard(
              context: context,
              title: "Sair (Logout)",
              icon: Icons.logout,
              routeName: "/logout", 
              color: Colors.red.shade700,
            ),
          ],
        ),
      ),
    );
  }
}
