import 'instituicao.dart';
import 'produto.dart';

class Venda {
  final int id;
  final int instituicaoId;
  final int produtoId;
  final int quantidade;
  final double valorTotal;
  final Instituicao? instituicao;
  final Produto? produto;

  Venda({
    required this.id,
    required this.instituicaoId,
    required this.produtoId,
    required this.quantidade,
    required this.valorTotal,
    this.instituicao,
    this.produto,
  });

  factory Venda.fromJson(Map<String, dynamic> json) {
    return Venda(
      id: json['id'],
      instituicaoId: json['instituicao_id'],
      produtoId: json['produto_id'],
      quantidade: json['quantidade'],
      valorTotal: double.parse(json['valor_total'].toString()),
      instituicao: json['instituicao'] != null ? Instituicao.fromJson(json['instituicao']) : null,
      produto: json['produto'] != null ? Produto.fromJson(json['produto']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'instituicao_id': instituicaoId,
      'produto_id': produtoId,
      'quantidade': quantidade,
      // 'valor_total' é calculado no backend
    };
  }
}
