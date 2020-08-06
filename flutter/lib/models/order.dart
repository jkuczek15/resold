class Order {

  final int customerId;
  final double total;
  final String status;
  final DateTime created;
  final DateTime updated;
  final List<OrderLine> items;

  Order({this.customerId, this.total, this.status, this.created, this.updated, this.items});

  factory Order.fromJson(dynamic doc) {
    try {
      var items = doc['items'].toList();
      List<OrderLine> orderLines = new List<OrderLine>();
      items.forEach((item) {
        orderLines.add(OrderLine.fromJson(item));
      });
      return Order (
        customerId: int.tryParse(doc['customer_id'].toString()),
        total: double.tryParse(doc['grand_total'].toString()),
        status: doc['status'].toString(),
        created: DateTime.tryParse(doc['created_at'].toString()),
        updated: DateTime.tryParse(doc['updated_at'].toString()),
        items: orderLines
      );
    } catch (exception) {
      return Order();
    }
  }
}

class OrderLine {

  final int productId;
  final String productSku;
  final String name;
  final double price;

  OrderLine({this.productId, this.productSku, this.name, this.price});

  factory OrderLine.fromJson(dynamic doc) {
    try {
      return OrderLine (
        productId: int.tryParse(doc['product_id'].toString()),
        productSku: doc['sku'].toString(),
        name: doc['name'].toString(),
        price: double.tryParse(doc['price'].toString())
      );
    } catch (exception) {
      return OrderLine();
    }
  }
}
