mod utils;
mod network;


fn main() {
    let mut api = network::WebStorage{base_url: utils::get_input("enter server address: ").to_string()};
    api.base_url = api.base_url.trim_end().to_string();
    loop {
        let command = utils::get_input("enter command: ").trim_end().to_string();
        if command.len() < 1 {
            println!("invalid command.");
            continue;
        }
        else {
            let first_char = command.chars().next().expect("invalid first char?");
            if first_char == 'g' {
                let creds = api.get_creds().expect("unable to get creds from server");
                for cred in creds {
                    println!("{}", cred)
                }
            }
            else if first_char == 's' {
                let mut cred = network::Credential{username: "".to_string(), password: "".to_string(), site: "".to_string(), time: "".to_string()};
                cred.username = utils::get_input("enter username to send to server: ");
                cred.password = utils::get_input("enter password to send to server: ");
                cred.site = utils::get_input("enter site to send to server: ");
                
                cred.time = utils::get_epoch_time().to_string();
                api.send_credentials(cred);
            }
            else {
                println!("invalid command.");
            }
        }
    }
}
