using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Globalization;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using MySql.Data.MySqlClient;


namespace RestaurantMenuManagementSystem
{
    public partial class Form1 : Form
    {
        string connectionString = "server=localhost;user id=root;password=fate;database=restaurant_db;";
        public Form1()
        {
            InitializeComponent();
        }

        private void Form1_Load(object sender, EventArgs e)
        {
            LoadMenu();    
        }

        private void LoadMenu()
        {
            using (MySqlConnection conn = new MySqlConnection(connectionString))
            {
                conn.Open();
                string query = "SELECT * FROM menu_items";

                MySqlDataAdapter adapter = new MySqlDataAdapter(query, conn);
                DataTable dt = new DataTable();
                adapter.Fill(dt);
                dgvMenuItems.DataSource = dt;
            }
        }

        private void btnAdd_Click(object sender, EventArgs e)
        {
            if (!ValidateInputs()) return;

            decimal price;
            if (!decimal.TryParse(txtPrice.Text, NumberStyles.Any, CultureInfo.InvariantCulture, out price))
            {
                MessageBox.Show("Invalid price format. Please enter a number like 12.50", "Error", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            using (MySqlConnection conn = new MySqlConnection(connectionString))
            {
                string query = "INSERT INTO menu_items (name, category, price, available) VALUES (@name, @category, @price, @available)";
                MySqlCommand cmd = new MySqlCommand(query, conn);
                cmd.Parameters.AddWithValue("@name", txtName.Text.Trim());
                cmd.Parameters.AddWithValue("@category", cmbCategory.SelectedItem.ToString());
                cmd.Parameters.AddWithValue("@price", price);
                bool available = cmbAvailability.SelectedItem.ToString() == "Available";
                cmd.Parameters.AddWithValue("@available", available);

                conn.Open();
                cmd.ExecuteNonQuery();
                conn.Close();
            }

            MessageBox.Show("Item added successfully!", "Success", MessageBoxButtons.OK, MessageBoxIcon.Information);
            LoadMenu();         
        }

        private bool ValidateInputs()
        {
            bool isValid = true;

            //Validate name
            if (string.IsNullOrWhiteSpace(txtName.Text))
            {
                label1.Text = "This field is required.";
                label1.Visible = true;
                isValid = false;
            }
            else if (txtName.Text.All(char.IsDigit))
            {
                label1.Text = "Please enter a valid name.";
                label1.Visible = true;
                isValid = false;
            }
            else
            {
                label1.Visible = false;
            }

            //Validate Category
            if (cmbCategory.SelectedIndex < 0)
            {
                label2.Text = "Please enter a category.";
                label2.Visible = true;
                isValid = false;
            }
            else
            {
                label2.Visible = false;
            }

            //Validate price
            if (!decimal.TryParse(txtPrice.Text, NumberStyles.Any, CultureInfo.InvariantCulture, out decimal price))
            {
                label3.Text = "Invalid price format. Enter number like 10.50";
                label3.Visible = true;
                isValid = false;
            }
            else
            {
                label3.Visible = false;
            }

            // Validate availability
            if (cmbAvailability.SelectedIndex < 0)
            {
                label4.Text = "Please select an availability option.";
                label4.Visible = true;
                isValid = false;
            }
            else
            {
                label4.Visible = false;
            }

            return isValid;
        }



        public void ClearFields()
        {
            txtName.Clear();
            cmbCategory.SelectedIndex = -1;
            txtPrice.Clear();
            cmbAvailability.SelectedIndex = -1;
        }

        private void btnUpdate_Click(object sender, EventArgs e)
        {
            if (dgvMenuItems.CurrentRow == null)
            {
                MessageBox.Show("Please select an item to update.", "Warning", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            if (!ValidateInputs()) return;


            int id = Convert.ToInt32(dgvMenuItems.CurrentRow.Cells["id"].Value);

            using (MySqlConnection conn = new MySqlConnection(connectionString))
            {
                string query = "UPDATE menu_items SET name=@Name, category=@Category, price=@Price, available=@Available WHERE id=@ID";
                MySqlCommand cmd = new MySqlCommand(query, conn);
                cmd.Parameters.AddWithValue("@name", txtName.Text.Trim());
                cmd.Parameters.AddWithValue("@category", cmbCategory.SelectedItem.ToString());
                cmd.Parameters.AddWithValue("@price", Convert.ToDecimal(txtPrice.Text));
                cmd.Parameters.AddWithValue("@available", cmbAvailability.SelectedItem.ToString() == "Available");
                cmd.Parameters.AddWithValue("@id", id);

                conn.Open();
                cmd.ExecuteNonQuery();
                conn.Close();
            }

            MessageBox.Show("Item updated successfully!", "Success", MessageBoxButtons.OK, MessageBoxIcon.Information);
            LoadMenu();
        }

        private void btnDelete_Click(object sender, EventArgs e)
        {
            if (dgvMenuItems.CurrentRow == null)
            {
                MessageBox.Show("Please select an item to delete.", "Warning", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            int id = Convert.ToInt32(dgvMenuItems.CurrentRow.Cells["id"].Value);
            DialogResult result = MessageBox.Show("Are you sure you want to delete this item?", "Confirm Delete",
                                                  MessageBoxButtons.YesNo, MessageBoxIcon.Question);

            if (result == DialogResult.No) return;

            using (MySqlConnection conn = new MySqlConnection(connectionString))
            {
                string query = "DELETE FROM menu_items WHERE id=@id";
                MySqlCommand cmd = new MySqlCommand(query, conn);
                cmd.Parameters.AddWithValue("@id", id);

                conn.Open();
                cmd.ExecuteNonQuery();
                conn.Close();
            }

            MessageBox.Show("Item deleted successfully!", "Deleted", MessageBoxButtons.OK, MessageBoxIcon.Information);
            LoadMenu();
        }

        private void btnClear_Click(object sender, EventArgs e)
        {
            txtName.Clear();
            cmbCategory.SelectedIndex = -1;
            txtPrice.Clear();
            cmbAvailability.SelectedIndex = -1;
        }

        private void dgvMenuItems_SelectionChanged_1(object sender, EventArgs e)
        {
            if (dgvMenuItems.CurrentRow == null) return;

            DataGridViewRow row = dgvMenuItems.CurrentRow;

            txtName.Text = row.Cells["name"].Value.ToString();
            txtPrice.Text = row.Cells["price"].Value.ToString();

            string category = row.Cells["category"].Value.ToString();
            cmbCategory.SelectedItem = cmbCategory.Items.Contains(category) ? category : null;

            object availableValue = row.Cells["available"].Value;
            bool available = false;

            if (availableValue != null && availableValue != DBNull.Value)
            {
                if (int.TryParse(availableValue.ToString(), out int intVal))
                    available = intVal != 0;
                else if (bool.TryParse(availableValue.ToString(), out bool boolVal))
                    available = boolVal;
            }

            cmbAvailability.SelectedItem = available ? "Available" : "Not Available";

            label1.Visible = label2.Visible = label3.Visible = label4.Visible = false;
        }
    }
}
