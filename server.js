const express = require('express');
const bodyParser = require('body-parser');
const bcrypt = require('bcryptjs');

const app = express();
app.use(bodyParser.json());

// Dummy data storage (replace with a real database later)
let users = [];
let orders = [];

// Basic routes
app.post('/register', (req, res) => {
  const { name, email, password } = req.body;

  // Hash the password before saving
  bcrypt.hash(password, 10, (err, hashedPassword) => {
    if (err) {
      return res.status(500).json({ message: 'Error hashing password' });
    }

    // Save the user to the dummy data storage
    const newUser = { name, email, password: hashedPassword };
    users.push(newUser);
    res.status(201).json({ message: 'User registered successfully', user: newUser });
  });
});

app.post('/order', (req, res) => {
  const { userEmail, productId, quantity, price } = req.body;
  const totalCost = price * quantity;
  const order = { userEmail, productId, quantity, price, totalCost, date: new Date() };

  orders.push(order);
  res.status(201).json({ message: 'Order placed successfully', order });
});

app.listen(3000, () => {
  console.log('Server is running on http://localhost:3000');
});
