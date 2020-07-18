import 'package:flutter/material.dart';
import './services/resold.dart' as resold;
import './models/product.dart';
import './builders/product-list-builder.dart';
import 'package:flappy_search_bar/flappy_search_bar.dart';

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
          primarySwatch: const MaterialColor(0xff257292, {
            50:  Color.fromRGBO(25,72,92, .1),
            100: Color.fromRGBO(25,72,92, .2),
            200: Color.fromRGBO(25,72,92, .3),
            300: Color.fromRGBO(25,72,92, .4),
            400: Color.fromRGBO(25,72,92, .5),
            500: Color.fromRGBO(25,72,92, .6),
            600: Color.fromRGBO(25,72,92, .7),
            700: Color.fromRGBO(25,72,92, .8),
            800: Color.fromRGBO(25,72,92, .9),
            900: Color.fromRGBO(25,72,92, 1)
          }),
          accentColor: Colors.white,
          primaryColor: const Color(0xff257292)
        ),
        home: HomePage()
    );
  }
}

class HomePageState extends State<HomePage> {
  int selectedIndex = 0;

  Future<List<Product>> futureLocalProducts;

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
    futureLocalProducts = resold.Api.fetchLocalProducts();
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
        fixedColor: const Color(0xff257292),
        unselectedItemColor: Colors.black,
        onTap: onItemTapped,
      ),
    );
  }

  Object getContent() {
    switch(selectedIndex) {
      case 0:
        return FutureBuilder<List<Product>>(
          future: futureLocalProducts,
          builder: (context, snapshot) {
            if (snapshot.hasData) {
              return ProductListBuilder.buildProductList(snapshot.data);
            } else if (snapshot.hasError) {
              return Text("${snapshot.error}");
            }
            // By default, show a loading spinner.
            return CircularProgressIndicator();
          },
        );
      case 1:
        return
          Stack(
            children: [
              SafeArea (
                child: SearchBar<Product>(
                  searchBarPadding: EdgeInsets.symmetric(horizontal: 20),
                  onSearch: resold.Api.fetchSearchProducts,
                  onItemFound: (Product product, int index) {
                    return ProductListBuilder.buildProductTile(product);
                  },
                ),
              )
            ]
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
