import 'package:flutter/material.dart';
import './services/resold.dart' as resold;
import './models/product.dart';

void main() {
  runApp(Resold());
}

class Resold extends StatelessWidget {
  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
        title: 'Resold',
        theme: ThemeData(
          primarySwatch: Colors.blue,
          accentColor: Colors.white,
          primaryColor: Colors.blue
        ),
        home: HomePage()
    );
  }
}

class HomePageState extends State<HomePage> {
  int selectedIndex = 0;
  String baseImagePath = 'https://s3-us-west-2.amazonaws.com/resold-photos/catalog/product';

  Future<List<Product>> futureProducts;

  final widgetOptions = [
    Text('Buy'),
    Text('Search'),
    Text('Sell'),
    Text('Orders'),
    Text('Account')
  ];

  @override
  void initState() {
    super.initState();
    futureProducts = resold.Api.fetchProducts();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Resold'),
      ),
      body: Center(
        child: getContent(),
      ),
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        items: <BottomNavigationBarItem>[
          BottomNavigationBarItem(icon: Icon(Icons.pin_drop), title: Text('Buy')),
          BottomNavigationBarItem(icon: Icon(Icons.search), title: Text('Search')),
          BottomNavigationBarItem(icon: Icon(Icons.add_box), title: Text('Sell')),
          BottomNavigationBarItem(icon: Icon(Icons.receipt), title: Text('Orders')),
          BottomNavigationBarItem(icon: Icon(Icons.person), title: Text('Account')),
        ],
        currentIndex: selectedIndex,
        fixedColor: Colors.blue,
        unselectedItemColor: Colors.black,
        onTap: onItemTapped,
      ),
    );
  }

  Object getContent() {
    switch(selectedIndex) {
      case 0:
        return FutureBuilder<List<Product>>(
          future: futureProducts,
          builder: (context, snapshot) {
            if (snapshot.hasData) {
              return ListView.builder(
                itemCount: snapshot.data.length,
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
                                      child: Image.network(baseImagePath + snapshot.data[index].thumbnail, width: 150, height: 150)
                                    ),
                                    Align(
                                      alignment: Alignment.centerLeft,
                                      child: Text(snapshot.data[index].name)
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
            } else if (snapshot.hasError) {
              return Text("${snapshot.error}");
            }
            // By default, show a loading spinner.
            return CircularProgressIndicator();
          },
        );
      default:
        return widgetOptions.elementAt(selectedIndex);
    }
  }

  void onItemTapped(int index) {
    setState(() {
      selectedIndex = index;
    });
  }
}

class HomePage extends StatefulWidget {
  HomePage({Key key}) : super(key: key);

  @override
  HomePageState createState() => HomePageState();
}
