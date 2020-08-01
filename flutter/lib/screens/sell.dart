import 'package:flutter/material.dart';
import 'package:resold/widgets/image-uploader.dart';

class SellPage extends StatefulWidget {
  SellPage({Key key}) : super(key: key);

  @override
  SellPageState createState() => SellPageState();
}

class SellPageState extends State<SellPage> {
  @override
  Widget build(BuildContext context) {
    return Padding (
      padding: EdgeInsets.fromLTRB(20, 20, 20, 20),
      child: Column (
        mainAxisAlignment: MainAxisAlignment.start,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Images:'),
          SizedBox(height: 5),
          ImageUploader()
        ]
      )
    );
  }
}
