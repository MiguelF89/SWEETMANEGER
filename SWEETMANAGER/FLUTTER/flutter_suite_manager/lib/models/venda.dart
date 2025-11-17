

class Venda {
  final int id;
  final int produtoId;
  final int quantidade;
  final double valorTotal;


  Venda({
    required this.id,
    required this.produtoId,
    required this.quantidade,
    required this.valorTotal,
  });

  factory Venda.fromJson(Map<String, dynamic> json) {
    return Venda(
      id: json['id'] as int,
      produtoId: json['produto_id'] as int,
      quantidade: json['quantidade'] as int,

      valorTotal: (json['valor_total'] is String) 
          ? double.parse(json['valor_total']) 
          : (json['valor_total'] as num).toDouble(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'produto_id': produtoId,
      'quantidade': quantidade,
      'valor_total': valorTotal,
    };
  }
}
