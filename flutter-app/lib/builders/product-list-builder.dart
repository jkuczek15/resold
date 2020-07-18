import 'package:flutter/material.dart';
import '../models/product.dart';

class ProductListBuilder {

  static String baseImagePath = 'https://s3-us-west-2.amazonaws.com/resold-photos/catalog/product';

  static ListView buildProductList(List<Product> data) {
    return ListView.builder(
      itemCount: data.length,
      itemBuilder: (context, index) {
        return ListTile(
            title: Card(
              child: InkWell(
                  splashColor: Colors.blue.withAlpha(30),
                  onTap: () { /* ... */ },
                  child: Container(
                    decoration: BoxDecoration(color: Colors.white),
                    child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Container (
                              padding: EdgeInsets.fromLTRB(20, 30, 20, 20),
                              child: Column (
                                children: [
                                  Align(
                                      alignment: Alignment.topLeft,
                                      child: Image.network(baseImagePath + data[index].thumbnail, width: 150, height: 150)
                                  ),
                                  Align(
                                      alignment: Alignment.centerLeft,
                                      child: Text(data[index].name)
                                  )
                                ],
                              )
                          )
                        ]
                    ),
                  )
              ),
            )
        );
      },
    );
  }
}