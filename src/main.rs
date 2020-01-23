mod network;
mod utils;

fn main() {
    let api = network::WebStorage {
        base_url: utils::get_input("enter server address: "),
    };
    loop {
        let command = utils::get_input("enter command: ");
        if command.len() < 1 {
            println!("invalid command.");
            continue;
        } else {
            let first_char = command.chars().next().expect("invalid first char?");
            if first_char == 'g' {
                let creds = match api.get_creds() {
                    Some(val) => val.values, //get the Vec<Credentials> out of the CredentialList
                    None => Default::default(),
                };
                
                if creds.len() > 0 {
                    for cred in creds {
                        println!("{}", cred)
                    }
                }
                else {
                    println!("no credentials recieved from server.");
                }
            } else if first_char == 's' {
                let mut cred: network::Credential = Default::default();

                cred.username = utils::get_input("enter username to send to server: ");
                cred.password = utils::get_input("enter password to send to server: ");
                cred.site = utils::get_input("enter site to send to server: ");
                cred.time = utils::get_epoch_time().to_string();

                api.send_credentials(cred);
            } else {
                println!("invalid command.");
            }
        }
    }
}
