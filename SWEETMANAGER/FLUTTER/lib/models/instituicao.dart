class Instituicao {
  final int id;
  final String nome;
  final String contato;
  final String cnpj;

  Instituicao({
    required this.id,
    required this.nome,
    required this.contato,
    required this.cnpj,
  });

  factory Instituicao.fromJson(Map<String, dynamic> json) {
    return Instituicao(
      id: json['id'],
      nome: json['nome'],
      contato: json['contato'],
      cnpj: json['cnpj'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'nome': nome,
      'contato': contato,
      'cnpj': cnpj,
    };
  }
}
